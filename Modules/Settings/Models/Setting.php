<?php

namespace Modules\Settings\Models;

use App\Traits\HasImageCleanup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasImageCleanup;

    /**
     * Settings keys that hold secrets. These values are encrypted at rest and
     * must NEVER leave the server. They are excluded from every browser-bound
     * payload (Inertia shared props, Livewire state, public API responses).
     */
    public const SECRET_KEYS = [
        'recaptcha_secret_key',
        'seo_ga4_service_account_json',
    ];

    /**
     * Settings keys that are intentionally public and safe to expose to the
     * frontend. This is the ONLY source used to build the public Inertia
     * "settings" prop. Everything else is private by default.
     */
    public const PUBLIC_KEYS = [
        'recaptcha_site_key',
        'seo_ga4_id',
        'seo_gtm_id',
        'seo_gsc_verification',
        'site_logo_header',
        'site_logo_footer',
        'site_logo_icon',
        'site_logo_preloader',
        'header_button_visible',
        'header_button_text',
        'header_button_url',
        'whatsapp_number',
        'office_hours',
        'instagram_username',
        'instagram_url',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'youtube_url',
        'map_url',
        'vion_is_active',
        'vion_welcome_message',
        'vion_starter_buttons',
        'vion_whatsapp_number',
    ];

    /**
     * Marker prefix used to identify values encrypted by this application.
     */
    private const ENCRYPTED_PREFIX = 'encrypted:';

    protected $guarded = [];

    protected static function booted(): void
    {
        // Encrypt secret values automatically on write. Any code path that
        // persists a plaintext secret (admin forms, seeders, imports) is
        // secured without relying on the caller to remember to encrypt.
        static::saving(function (Setting $setting) {
            if ($setting->isDirty('value') && static::isSecretKey((string) $setting->key)) {
                $value = $setting->value;

                if (filled($value) && ! static::isEncrypted($value)) {
                    $setting->value = static::ENCRYPTED_PREFIX.Crypt::encryptString($value);
                }
            }
        });
    }

    public static function isSecretKey(string $key): bool
    {
        return in_array($key, self::SECRET_KEYS, true);
    }

    public static function isEncrypted(?string $value): bool
    {
        return is_string($value) && str_starts_with($value, self::ENCRYPTED_PREFIX);
    }

    /**
     * Resolve a setting value server-side.
     *
     * For secret keys the stored ciphertext is decrypted before being
     * returned. This method is for SERVER-SIDE consumers only; secret keys are
     * never included in the public allow-list used for browser payloads.
     */
    public static function getValue(string $key, $default = null)
    {
        $value = static::where('key', $key)->value('value');

        if ($value === null) {
            return $default;
        }

        return static::decryptValue((string) $key, $value);
    }

    /**
     * Decrypt a raw stored value when it belongs to a secret key.
     * Plaintext (e.g. pre-migration data) is returned untouched.
     */
    public static function decryptValue(string $key, ?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! static::isSecretKey($key) || ! static::isEncrypted($value)) {
            return $value;
        }

        try {
            return Crypt::decryptString(substr($value, strlen(self::ENCRYPTED_PREFIX)));
        } catch (\Throwable $e) {
            // Returning the raw stored payload keeps the application from
            // crashing when the encryption key has changed. It is ciphertext,
            // not the plaintext secret.
            return $value;
        }
    }

    /**
     * The allow-listed keys that are safe to expose to the public frontend.
     *
     * @return array<int, string>
     */
    public static function publicKeys(): array
    {
        return self::PUBLIC_KEYS;
    }

    /**
     * Fetch ONLY the public settings that the frontend is allowed to see.
     * Secrets are never returned by this method.
     *
     * @return array<string, string>
     */
    public static function getPublicSettings(): array
    {
        return static::whereIn('key', self::PUBLIC_KEYS)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function getCleanupFields(): array
    {
        $imageKeys = ['seo_favicon', 'seo_default_og_image', 'site_logo_header', 'site_logo_footer', 'site_logo_icon'];

        if (in_array($this->key, $imageKeys)) {
            return ['value'];
        }

        return [];
    }
}

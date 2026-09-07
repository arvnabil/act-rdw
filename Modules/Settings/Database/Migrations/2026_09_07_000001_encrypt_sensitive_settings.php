<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Encrypts existing plaintext values of secret settings at rest.
 *
 * Secret keys must never leave the server; this migration only protects the
 * previously stored values. New writes are already encrypted by the
 * \Modules\Settings\Models\Setting model.
 */
return new class extends Migration
{
    private const SECRET_KEYS = [
        'recaptcha_secret_key',
        'seo_ga4_service_account_json',
    ];

    private const ENCRYPTED_PREFIX = 'encrypted:';

    public function up(): void
    {
        foreach (self::SECRET_KEYS as $key) {
            $rows = DB::table('settings')->where('key', $key)->get(['id', 'value']);

            foreach ($rows as $row) {
                if (filled($row->value) && ! str_starts_with((string) $row->value, self::ENCRYPTED_PREFIX)) {
                    DB::table('settings')->where('id', $row->id)->update([
                        'value' => self::ENCRYPTED_PREFIX.Crypt::encryptString((string) $row->value),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach (self::SECRET_KEYS as $key) {
            $rows = DB::table('settings')->where('key', $key)->get(['id', 'value']);

            foreach ($rows as $row) {
                if (filled($row->value) && str_starts_with((string) $row->value, self::ENCRYPTED_PREFIX)) {
                    try {
                        $plain = Crypt::decryptString(substr((string) $row->value, strlen(self::ENCRYPTED_PREFIX)));

                        DB::table('settings')->where('id', $row->id)->update(['value' => $plain]);
                    } catch (Throwable $e) {
                        // Leave the value encrypted when it cannot be decrypted.
                    }
                }
            }
        }
    }
};

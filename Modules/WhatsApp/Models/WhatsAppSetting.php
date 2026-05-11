<?php

namespace Modules\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppSetting extends Model
{
    protected $fillable = [
        'is_enabled',
        'phone',
        'message',
        'tooltip',
        'position',
        'show_online_badge',
        'show_pulse_animation',
        'open_in_new_tab',
        'offset_bottom',
        'offset_side',
        'icon',
        'button_color',
        'active_pages',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'show_online_badge' => 'boolean',
        'show_pulse_animation' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'active_pages' => 'array',
    ];

    /**
     * Get the singleton instance of WhatsAppSetting.
     */
    public static function getInstance()
    {
        return self::first() ?? self::create([
            'is_enabled' => true,
            'phone' => '6281280944719',
            'message' => 'Halo ACTiV, saya ingin konsultasi solusi ICT',
            'tooltip' => 'Butuh bantuan? Chat kami di WhatsApp',
            'position' => 'bottom-right',
            'show_online_badge' => true,
            'show_pulse_animation' => true,
            'open_in_new_tab' => true,
            'offset_bottom' => '110px',
            'offset_side' => '24px',
            'button_color' => '#25D366',
            'active_pages' => ['all'],
        ]);
    }
}

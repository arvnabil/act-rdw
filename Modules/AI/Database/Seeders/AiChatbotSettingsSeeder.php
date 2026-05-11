<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\Setting;

class AiChatbotSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'key' => 'vion_is_active',
                'label' => 'Enable Vion Assistant',
                'value' => '1',
            ],
            [
                'key' => 'vion_welcome_message',
                'label' => 'Vion Welcome Message',
                'value' => 'Halo [Nama]! 👋 Saya Vion, ICT Solutions Consultant Anda. Ada yang bisa saya bantu hari ini? Silakan pilih topik di bawah atau ketik pertanyaan Anda.',
            ],
            [
                'key' => 'vion_starter_buttons',
                'label' => 'Vion Starter Buttons',
                'value' => json_encode([
                    [
                        'label' => '📍 Lokasi Kantor',
                        'message' => 'Dimana lokasi kantor ACTiV?',
                        'instant_response' => "Kantor kami (PT Alfa Cipta Teknologi Virtual) berlokasi di:\n\n**Grand Slipi Tower Lt. 9 Unit O, Jl. S. Parman Kav. 22-24, Jakarta Barat.**\n\nAnda bisa mengunjungi kami menggunakan Google Maps: [Klik di Sini](https://maps.google.com/?q=Grand+Slipi+Tower)",
                    ],
                    [
                        'label' => '📞 Hubungi Kami',
                        'message' => 'Saya ingin menghubungi tim ACTiV',
                        'instant_response' => "Anda dapat menghubungi tim kami melalui saluran berikut:\n\n💬 **WhatsApp**: 6285162994602\n📧 **Email**: sales@activ.co.id\n☎️ **Telepon**: (021) 29865940",
                    ],
                    [
                        'label' => '📱 Media Sosial',
                        'message' => 'Apa media sosial ACTiV?',
                        'instant_response' => "Ikuti kami untuk update terbaru mengenai solusi ICT:\n\n📸 **Instagram**: [@activ_teknologi](https://www.instagram.com/activ_teknologi/)\n💼 **LinkedIn**: [PT Alfa Cipta Teknologi Virtual](https://www.linkedin.com/company/activ-teknologi/)\n🌐 **Website**: [www.activ.co.id](https://www.activ.co.id)",
                    ],
                    [
                        'label' => '⏰ Jam Operasional',
                        'message' => 'Kapan jam kerja ACTiV?',
                        'instant_response' => "Tim kami siap melayani Anda pada:\n\n📅 **Senin - Jumat**\n🕙 **08:30 - 17:30 WIB**\n\n*Sabtu, Minggu, dan Libur Nasional kantor kami tutup.*",
                    ],
                    [
                        'label' => '💼 Solusi Meeting Room',
                        'message' => 'Saya tertarik dengan solusi meeting room',
                        'instant_response' => '', // Dijawab oleh AI
                    ],
                    [
                        'label' => '🛠️ Konsultasi Teknis',
                        'message' => 'Saya butuh bantuan teknis / penawaran harga',
                        'instant_response' => '', // Dijawab oleh AI
                    ],
                ]),
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['label' => $setting['label'], 'value' => $setting['value']]
            );
        }
    }
}

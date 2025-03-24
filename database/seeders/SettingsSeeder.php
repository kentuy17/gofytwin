<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Settings;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settings::create([
            'field' => 'favicon',
            'value' => 'images/favicon.ico'
        ]);
        Settings::create([
            'field' => 'logo_image',
            'value' => 'images/logo.png'
        ]);
        Settings::create([
            'field' => 'header_image',
            'value' => 'images/header.jpg'
        ]);
        Settings::create([
            'field' => 'banner_image',
            'value' => 'images/homepage-banner.png'
        ]);
        Settings::create([
            'field' => 'background_image',
            'value' => 'images/site-bg.png'
        ]);
        Settings::create([
            'field' => 'og_image',
            'value' => 'images/site-bg.png'
        ]);
        Settings::create([
            'field' => 'is_maintenance',
            'value' => 'no'
        ]);Settings::create([
            'field' => 'maintenance_text',
            'value' => 'Sorry, plays are not available today.'
        ]);
        Settings::create([
            'field' => 'custom_date',
            'value' => ''
        ]);
        Settings::create([
            'field' => 'messenger_link',
            'value' => ''
        ]);
        Settings::create([
            'field' => 'telegram_link',
            'value' => ''
        ]);


        Settings::create([
            'field' => 'sms_sender',
            'value' => 'SALAZAR'
        ]);
        Settings::create([
            'field' => 'sms_api_key',
            'value' => '501575f0b60e5f9976b549c8a6d63701'
        ]);
        Settings::create([
            'field' => 'sms_message_endpoint',
            'value' => 'https://api.semaphore.co/api/v4/messages'
        ]);
        Settings::create([
            'field' => 'otp_endpoint',
            'value' => 'https://api.semaphore.co/api/v4/otp'
        ]);
        Settings::create([
            'field' => 'account_endpoint',
            'value' => 'https://api.semaphore.co/api/v4/accoun'
        ]);
    }
}

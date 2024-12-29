<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'group' => 'general',
                'key' => 'site_name',
                'value' => 'My CMS',
                'type' => 'text',
                'display_name' => 'Site Name',
                'description' => 'The name of your website',
                'is_public' => true,
            ],
            [
                'group' => 'general',
                'key' => 'site_description',
                'value' => 'A powerful CMS built with Laravel',
                'type' => 'textarea',
                'display_name' => 'Site Description',
                'description' => 'A brief description of your website',
                'is_public' => true,
            ],
            
            // Contact Settings
            [
                'group' => 'contact',
                'key' => 'contact_email',
                'value' => 'contact@example.com',
                'type' => 'text',
                'display_name' => 'Contact Email',
                'description' => 'Primary contact email address',
                'is_public' => true,
            ],
            [
                'group' => 'contact',
                'key' => 'phone_number',
                'value' => '+1234567890',
                'type' => 'text',
                'display_name' => 'Phone Number',
                'description' => 'Primary contact phone number',
                'is_public' => true,
            ],
            
            // Social Media Settings
            [
                'group' => 'social',
                'key' => 'facebook_url',
                'value' => 'https://facebook.com',
                'type' => 'text',
                'display_name' => 'Facebook URL',
                'description' => 'Facebook page URL',
                'is_public' => true,
            ],
            [
                'group' => 'social',
                'key' => 'twitter_url',
                'value' => 'https://twitter.com',
                'type' => 'text',
                'display_name' => 'Twitter URL',
                'description' => 'Twitter profile URL',
                'is_public' => true,
            ],
            
            // SEO Settings
            [
                'group' => 'seo',
                'key' => 'meta_keywords',
                'value' => 'cms,laravel,web',
                'type' => 'textarea',
                'display_name' => 'Meta Keywords',
                'description' => 'Default meta keywords for SEO',
                'is_public' => true,
            ],
            [
                'group' => 'seo',
                'key' => 'google_analytics_id',
                'value' => '',
                'type' => 'text',
                'display_name' => 'Google Analytics ID',
                'description' => 'Google Analytics tracking ID',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'app.name', 'value' => ['Nataye Smart Education System'], 'group' => 'general'],
            ['key' => 'app.timezone', 'value' => ['UTC'], 'group' => 'general'],
            ['key' => 'attendance.auto_lock_days', 'value' => [7], 'group' => 'attendance'],
            ['key' => 'exam.max_attempts', 'value' => [1], 'group' => 'exam'],
            ['key' => 'notification.email_enabled', 'value' => [true], 'group' => 'notification'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}

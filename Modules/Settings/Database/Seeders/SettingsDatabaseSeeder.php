<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            LocalBusinessSettingsSeeder::class,
            SettingSeeder::class,
        ]);
    }
}

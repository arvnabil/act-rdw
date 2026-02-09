<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'nabil@activ.co.id'],
            [
                'name' => 'Nabil',
                'password' => \Illuminate\Support\Facades\Hash::make('123123123'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            PageSeeder::class,
            ServicesPageSeeder::class,
            MenuSeeder::class,
            SettingSeeder::class,
        ]);

        // $this->call(\Modules\ServiceSolutions\Database\Seeders\ProductConfiguratorSeeder::class);

        // Auto-discover and run Module seeders
        $modules = glob(base_path('Modules/*'), GLOB_ONLYDIR);
        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $seederClass = "Modules\\{$moduleName}\\Database\\Seeders\\{$moduleName}DatabaseSeeder";

            if (class_exists($seederClass)) {
                $this->call($seederClass);
            }
        }
    }
}

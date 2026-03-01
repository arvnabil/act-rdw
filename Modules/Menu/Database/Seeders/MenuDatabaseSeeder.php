<?php

namespace Modules\Menu\Database\Seeders;

use Illuminate\Database\Seeder;

class MenuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MenuSeeder::class,
        ]);
    }
}

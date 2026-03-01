<?php

namespace Modules\CMS\Database\Seeders;

use Illuminate\Database\Seeder;

class CMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PageSeeder::class,
            ServicesPageSeeder::class,
        ]);
    }
}

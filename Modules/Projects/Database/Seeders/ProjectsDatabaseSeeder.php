<?php

namespace Modules\Projects\Database\Seeders;

use Illuminate\Database\Seeder;

class ProjectsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ProjectSeeder::class,
        ]);
    }
}

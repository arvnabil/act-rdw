<?php

namespace Modules\ServiceSolutions\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ServiceSolutionsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(ServiceSeeder::class);
        $this->call(ServiceSolutionSeeder::class);
        $this->call(ProductConfiguratorSeeder::class);
    }
}

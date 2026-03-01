<?php

namespace Modules\Clients\Database\Seeders;

use Illuminate\Database\Seeder;

class ClientsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ClientSeeder::class,
        ]);
    }
}

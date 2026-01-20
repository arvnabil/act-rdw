<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            ['name' => 'Client 1'],
            ['name' => 'Client 2'],
            ['name' => 'Client 3'],
            ['name' => 'Client 4'],
            ['name' => 'Client 5'],
            ['name' => 'Client 6'],
            ['name' => 'Client 7'],
            ['name' => 'Client 8'],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}

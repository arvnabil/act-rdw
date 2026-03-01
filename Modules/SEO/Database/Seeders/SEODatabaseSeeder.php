<?php

namespace Modules\SEO\Database\Seeders;

use Illuminate\Database\Seeder;

class SEODatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SeoMetaSeeder::class,
        ]);
    }
}

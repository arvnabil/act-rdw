<?php

namespace Modules\News\Database\Seeders;

use Illuminate\Database\Seeder;

class NewsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ActivNewsSeeder::class,
            NewsSeeder::class,
            NewsTaxonomySeeder::class,
        ]);
    }
}

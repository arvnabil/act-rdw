<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ModuleSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:seed {module? : The name of the module to seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with records for a specific module or all modules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName = $this->argument('module');
        
        if ($moduleName) {
            $this->seedModule($moduleName);
        } else {
            $this->seedAllModules();
        }
    }

    protected function seedAllModules()
    {
        $modules = glob(base_path('Modules/*'), GLOB_ONLYDIR);
        
        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $this->seedModule($moduleName);
        }
    }

    protected function seedModule($moduleName)
    {
        $className = "Modules\\{$moduleName}\\Database\\Seeders\\{$moduleName}DatabaseSeeder";

        if (class_exists($className)) {
            $this->info("Seeding module: {$moduleName}");
            $this->call('db:seed', ['--class' => $className]);
        } else {
            // Check if file exists but maybe autoloader hasn't picked it up (though psr-4 should work)
            // Or maybe user didn't create the specific class.
            // We can search for any Seeder in that folder if strict naming isn't followed? 
            // Better stick to convention: ModuleNameDatabaseSeeder
            if ($this->argument('module')) { // Only error if explicitly requested
                $this->error("Seeder class [{$className}] not found.");
            }
        }
    }
}

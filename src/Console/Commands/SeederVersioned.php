<?php

namespace CodeBider\VersionSeeder\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use CodeBider\VersionSeeder\Console\VersionedSeeder;

class SeederVersioned extends Command
{
     protected $signature = 'db:seed:versioned
                            {--connection= : Database connection name}
                            {--database= : Specific database name}
                            {--module= : Specific module to seed}';

    protected $description = 'Run versioned seeders in chronological order per module';

    public function handle()
    {
        $connection = $this->option('connection') ?? config('database.default');
        $database = $this->option('database');
        $module = $this->getSafeModuleName();

        try {
            VersionedSeeder::setConnection($connection, $database);
        } catch (\RuntimeException $e) {
            throw $e;
        }

        $seedersPath = $module
            ? database_path("seeders/Versioned/{$module}")
            : database_path('seeders/Versioned');

        if (!File::exists($seedersPath)) {
            $this->error("No seeders found for module: $module");
            return;
        }

        $files = File::files($seedersPath);
        $batch = VersionedSeeder::getNextBatchNumber();
        $seederClasses = [];

        $defaultSeederClass = null;
        $defaultSeederTime = null;

        foreach ($files as $file) {
            $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $className = $this->buildSeederClassName($fileName, $module);

            if (!class_exists($className)) {
                require_once $file->getPathname();
            }

            if (!class_exists($className)) {
                $this->error("Class {$className} not found in {$file->getFilename()}");
                continue;
            }

            $seeder = new $className;

            if (!$seeder->hasRun($className)) {
                if ($fileName === 'DefaultSeeder') {
                    $defaultSeederClass = $className;
                    $defaultSeederTime = $file->getCTime();
                } else {
                    $seederClasses[$className] = $file->getCTime();
                }
            }
        }

        // Sort remaining seeders by file creation time
        asort($seederClasses);

        // Run DefaultSeeder first if it exists
        if ($defaultSeederClass) {
            $this->callSilent('db:seed', ['--class' => $defaultSeederClass]);
            VersionedSeeder::markAsRun($defaultSeederClass, $batch, $module);
            $this->info("Seeded: {$defaultSeederClass}");
        }
        else{
            // Run all other seeders
            foreach ($seederClasses as $class => $time) {
                $this->callSilent('db:seed', ['--class' => $class]);
                VersionedSeeder::markAsRun($class, $batch, $module);
                $this->info("Seeded: {$class}");
            }
        }


        $this->info('All versioned seeders completed.');
    }


    private function getSafeModuleName(): ?string
    {
        $module = $this->option('module');
        return $module ? Str::studly($module) : null;
    }

    private function buildSeederClassName(string $fileName, ?string $module): string
    {
        return $module
            ? "Database\\Seeders\\Versioned\\{$module}\\{$fileName}"
            : "Database\\Seeders\\Versioned\\{$fileName}";
    }
}

<?php

namespace CodeBider\VersionSeeder\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class VersionedSeederMakeCommand extends GeneratorCommand
{
    protected $signature = 'make:seeder:versioned
                            {name : The seeder class name}
                            {--module= : The name of the module}';

    protected $description = 'Create a versioned seeder file with a timestamped filename inside the Versioned folder';

    protected $type = 'Seeder';

    protected function getStub()
    {
        $stubPath = base_path('stubs/versioned_seeder.stub');
        if (! File::exists($stubPath)) {
            $stubPath = __DIR__ . '/../../../stubs/versioned_seeder.stub';
        }
        $this->info($stubPath);
        return $stubPath;
    }

    protected function getPath($name)
    {
        $className = Str::studly($this->argument('name'));
        $module = $this->getSafeModuleName();

        $basePath = database_path('seeders/Versioned/');

        if ($module) {
            $basePath .= $module.'/';
        }

        File::ensureDirectoryExists($basePath);

        $fileName = $className.'.php';

        return $basePath.$fileName;
    }

    protected function buildClass($name)
    {
        $className = Str::studly($this->argument('name'));
        $stub = $this->files->get($this->getStub());

        return str_replace(
            ['DummySeeder', 'DummyNamespace'],
            [$className, $this->getRelativeNamespace()],
            $stub
        );
    }

    protected function getRelativeNamespace()
    {
        $module = $this->getSafeModuleName();

        return $module
            ? "Database\\Seeders\\Versioned\\$module"
            : "Database\\Seeders\\Versioned";
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $module = $this->getSafeModuleName();

        return $module
            ? $rootNamespace.'\\Seeders\\Versioned\\'.$module
            : $rootNamespace.'\\Seeders\\Versioned';
    }

    protected function getSafeModuleName()
    {
        $module = Str::studly($this->option('module'));

        return $module;
    }
}

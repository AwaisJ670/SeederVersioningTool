<?php

namespace CodeBider\VersionSeeder;

use Illuminate\Support\ServiceProvider;
use CodeBider\VersionSeeder\Console\Commands\SeederVersioned;
use CodeBider\VersionSeeder\Console\Commands\VersionedSeederMakeCommand;

class VersionSeederServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Load migrations from the package
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Register the command
            $this->commands([
                SeederVersioned::class,
                VersionedSeederMakeCommand::class,
            ]);

            // Publish the stub file
            $this->publishes([
                __DIR__.'/../stubs/versioned_seeder.stub' => base_path('stubs/versioned_seeder.stub'),
            ], 'stubs');

            $this->publishes([
                __DIR__.'/src/../database/migrations/create_seeders_table.php' =>
                    database_path('migrations/'.date('Y_m_d_His', time()).'_create_seeders_table.php'),
            ], 'migrations');

        }
    }
}

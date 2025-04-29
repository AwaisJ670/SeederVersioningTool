<?php

namespace CodeBider\VersionSeeder\Console;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class VersionedSeeder extends Seeder
{
    protected static $table = 'seeders';
    protected static $connectionName;
    protected static $databaseName;

    public static function setConnection(string $connection, string $database = null)
    {
        static::$connectionName = $connection;

        if ($database) {
            config(["database.connections.{$connection}.database" => $database]);
            DB::purge($connection);
            static::$databaseName = $database;
        }

        if (!Schema::connection(static::$connectionName)->hasTable(static::$table)) {
            throw new \RuntimeException("Seeders table missing on [{$connection}] connection". ($database ? " (database: {$database})" : ""));
        }
    }

    public static function hasRun($seederClass): bool
    {
        return DB::connection(static::$connectionName)->table(static::$table)->where('seeder', $seederClass)->exists();
    }

    public static function markAsRun($seederClass, $batch, $moduleName)
    {
        DB::connection(static::$connectionName)->table(static::$table)->insert([
            'module' => $moduleName,
            'seeder' => $seederClass,
            'batch' => $batch,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function getNextBatchNumber(): int
    {
        return DB::connection(static::$connectionName)->table(static::$table)->max('batch') + 1;
    }
}


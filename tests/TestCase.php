<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use LogicException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->forceLocalTestingDatabase();

        parent::setUp();

        $connection = config('database.default');
        $database = (string) config("database.connections.{$connection}.database");
        $host = (string) config("database.connections.{$connection}.host");

        if (!app()->environment('testing')
            || !str_ends_with($database, '_testing')) {
            throw new LogicException('Tests must use an isolated local database ending in _testing.');
        }
    }

    private function forceLocalTestingDatabase(): void
    {
        $dbHost = env('TEST_DB_HOST', '127.0.0.1');

        $variables = [
            'APP_ENV' => 'testing',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $dbHost,
            'DB_PORT' => '3306',
            'DB_DATABASE' => 'yvidlapc_tiemchung_testing',
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => '',
        ];

        foreach ($variables as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

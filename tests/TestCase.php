<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use LogicException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $connection = config('database.default');
        $database = (string) config("database.connections.{$connection}.database");
        $host = (string) config("database.connections.{$connection}.host");

        if (!app()->environment('testing')
            || !str_ends_with($database, '_testing')
            || !in_array($host, ['127.0.0.1', 'localhost'], true)) {
            throw new LogicException('Tests must use an isolated local database ending in _testing.');
        }
    }
}

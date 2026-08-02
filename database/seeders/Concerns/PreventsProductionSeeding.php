<?php

namespace Database\Seeders\Concerns;

use LogicException;

trait PreventsProductionSeeding
{
    protected function assertSafeSeedingTarget(): void
    {
        $connection = config('database.default');
        $database = (string) config("database.connections.{$connection}.database");

        if (app()->environment('production') || $database === 'yvidlapc_tiemchung') {
            throw new LogicException('Seeding is blocked for the production database. Use an isolated local or testing database.');
        }
    }
}

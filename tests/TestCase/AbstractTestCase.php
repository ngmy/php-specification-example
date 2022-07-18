<?php

declare(strict_types=1);

namespace Tests\TestCase;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;

abstract class AbstractTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * {@inheritdoc}
     */
    protected function getPackageAliases($app): array
    {
        return [
            'DB' => DB::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function defineEnvironment($app): void
    {
        /** @var Repository */
        $config = $app['config'];
        $config->set('database.default', 'testbench');
        $config->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    /**
     * {@inheritdoc}
     */
    protected function defineDatabaseSeeders(): void
    {
        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class]);
    }
}

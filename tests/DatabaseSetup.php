<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * This trait has to be used by a class that extends TestCase and will initialize (drop/create, migrate & seed)
 * a database while starting and rolling back transactions (optional) per test
 * @package Tests
 */
trait DatabaseSetup
{
    /**
     * flag to avoid migration running more than once per Test Case
     * @var bool
     */
    protected static $migrated = false;

    /**
     * the configured (physical) DB has to end with this suffix
     * we don't want incidents like this: https://github.blog/2010-11-15-today-s-outage/
     * @var string
     */
    public static $databaseSuffix = '-test';

    /**
     * this method will be called before each test - on setUp() - so that we ensure
     * 1) the DB migrations are up to date
     * 2) we have a DB connection established with the proper test DB
     */
    public function setupDatabase(): void
    {
        // in memory is not implemented as it was not the best approach for the RJR architecture
        if ($this->isInMemory()) {
            $this->setupInMemoryDatabase();
        }
        // physical Database
        else {
            // a separated _test DB will be used, migration will run once per TestCase and every test will be rolled back after finishing
            $this->setupTestDatabase();
        }
    }

    /**
     * called after each test (only physical DBs need rollback as in memory DBs are re-created for each test)
     */
    protected function tearDownDatabase():void
    {
        // this conf allows the test data to be persisted (for analysis)
        if (! $this->isInMemory() && ! env('DB_PERSIST_TEST_DATA', false)) {
            DB::rollback();
        }
    }

    /**
     * check the DB connection on the config
     * @return bool
     */
    protected function isInMemory(): bool
    {
        return config('database.connections')[config('database.default')]['database'] == ':memory:';
    }

    /**
     * :memory: database (SQLite) will be entirely build upon each test
     */
    protected function setupInMemoryDatabase(): void
    {
        throw new \Exception("In Memory Database has not been tested yet");

        // run migration for each test
        $this->migrate();

        $this->app[Kernel::class]->setArtisan(null);
    }

    /**
     * make sure the physical database is properly set
     * migration will be ran only once
     */
    protected function setupTestDatabase(): void
    {
        // ensure the testing environment was loaded properly
        $this->validateDatabase();

        // ran once per Test Case
        if (! static::$migrated) {

            $this->createDatabase();
            $this->migrate();
            $this->seedUser();

            static::$migrated = true;

        }

        // based on the config, each test will be rolled back after completing (see tearDown())
        if (! env('DB_PERSIST_TEST_DATA', false)) {
            DB::beginTransaction();
        }
    }

    /**
     * Let's ensure we have a fresh database
     * @return bool
     * @throws \Exception
     */
    protected function createDatabase(): void
    {
        $dbConnection = env('DB_CONNECTION');
        $dbHost = env('DB_HOST');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        try {

            $db = new \PDO("{$dbConnection}:host=$dbHost", $dbUser, $dbPass);

            $drop = $db->exec("DROP DATABASE IF EXISTS `{$dbName}`;");
            if($drop === false)
                throw new \Exception($db->errorInfo()[2]);

            $create = $db->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci';");
            if($create === false)
                throw new \Exception($db->errorInfo()[2]);

            $this->output("Database {$dbName} created");

        }
        catch (\Exception $e) {
            throw new \Exception(__METHOD__ . "@{$e->getLine()}: Database ({$dbName}) creation failed: {$e->getMessage()}");
        }
    }

    /**
     * run migrations through artisan command and dump output
     */
    protected function migrate(): void
    {
        $output = new BufferedOutput;

        // make sure the migration runs on the testing environment
        Artisan::call('migrate', ['--env' => 'testing'], $output);

        $this->output('Migration ran');

        // migration output
        if ($output->fetch()) {
            $this->output($output->fetch());
        }
    }

    /**
     * run migrations through artisan command and dump output
     */
    protected function seedUser(): void
    {
        $output = new BufferedOutput;

        // make sure the migration runs on the testing environment
        Artisan::call('db:seed', ['--env' => 'testing', '--class' => 'InitialData'], $output);

        $this->output('Initial Data Seeded');

        // migration output
        if ($output->fetch()) {
            $this->output($output->fetch());
        }
    }

    /**
     * confirms the database name includes the testing suffix
     * @return bool
     * @throws \Exception
     */
    protected function validateDatabase(): void
    {
        $suffix = self::$databaseSuffix;

        $length = strlen($suffix);

        $dbName = config('database.connections')[config('database.default')]['database'];

        // compare the end of the db name with the db suffix
        if(substr($dbName,-$length) != $suffix) {
            throw new \Exception("Invalid database name: {$dbName} (you wanna use the {$suffix} suffix in it)");
        }
    }
}

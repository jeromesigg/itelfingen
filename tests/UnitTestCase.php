<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Basis für Unit-Tests, die Laravel brauchen (config, Validator), aber keine Datenbank.
 * Ohne RefreshDatabase; eine Datenbankabfrage gilt als Fehler im Test.
 */
abstract class UnitTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        DB::listen(fn ($query) => throw new RuntimeException('Unit-Test darf keine Datenbank verwenden: '.$query->sql));
    }
}

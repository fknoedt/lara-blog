<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * centralized output method
     * @param $msg
     */
    public static function output($msg)
    {
        echo $msg . PHP_EOL;
    }
}

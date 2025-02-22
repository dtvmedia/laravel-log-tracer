<?php

namespace Dtvmedia\LaravelLogTracer\Tests\Fixture;

use Illuminate\Support\Facades\Log;

class DummyClass
{
    public function instance(): void
    {
        Log::debug('Test log message 3');
    }

    public static function static(): void
    {
        Log::debug('Test log message 4');
    }

    public static function closure1(): void
    {
        (fn () => Log::debug('Test log message 1'))();
    }

    public static function closure2(): void
    {
        (fn () => logger()->debug('Test log message 2'))();
    }
}

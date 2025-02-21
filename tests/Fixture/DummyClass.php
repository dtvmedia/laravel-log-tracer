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
}

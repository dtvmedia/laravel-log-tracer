<?php

namespace Dtvmedia\LaravelLogTracer\Tests\Fixture;

use Exception;
use Illuminate\Support\Facades\Log;

class DummyExceptionClass
{
    public static function logException(): void
    {
        Log::error(new Exception('Test log exception'));
    }
}

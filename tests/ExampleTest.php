<?php

use Dtvmedia\LaravelLogTracer\Tests\DummyClass;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->logFile = storage_path('logs/laravel.log');

    // clear logs
    File::put($this->logFile, '');
});

it('can log the source', function (string $message, string $expected, Closure $callable) {
    $callable();

    $logContents = File::get($this->logFile);

    expect($logContents)
        ->toContain($message)
        ->toContain($expected);
})->with([
    [
        'Test log message 1',
        '[ExampleTest::{closure:26}]',
        fn () => Log::debug('Test log message 1'),
    ],
    [
        'Test log message 2',
        '[ExampleTest::{closure:31}]',
        fn () => logger('Test log message 2'),
    ],
    [
        'Test log message 3',
        '[DummyClass::instance]',
        fn () => (new DummyClass)->instance(),
    ],
    [
        'Test log message 4',
        '[DummyClass::static]',
        fn () => DummyClass::static(),
    ],
]);

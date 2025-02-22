<?php

use Dtvmedia\LaravelLogTracer\Tests\Fixture\DummyClass;
use Dtvmedia\LaravelLogTracer\Tests\Fixture\DummyExceptionClass;
use Illuminate\Support\Facades\File;

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
        '[DummyClass::{closure:21}]',
        fn () => DummyClass::closure1(),
    ],
    [
        'Test log message 2',
        '[DummyClass::{closure:26}]',
        fn () => DummyClass::closure2(),
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

it('dont log the source for exceptions by default', function () {
    DummyExceptionClass::logException();

    $logContents = File::get($this->logFile);

    expect($logContents)
        ->not->toContain('[DummyExceptionClass::logException]')
        ->toContain('testing.ERROR: Exception: Test log exception');
});

it('can log the source for exceptions if enabled', function () {
    config()->set('log-tracer.ignore_exceptions', false);

    DummyExceptionClass::logException();

    $logContents = File::get($this->logFile);

    expect($logContents)
        ->toContain('testing.ERROR: [DummyExceptionClass::logException] Exception: Test log exception');
});

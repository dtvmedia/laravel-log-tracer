<?php

namespace Dtvmedia\LaravelLogTracer;

use Illuminate\Support\Stringable;
use Monolog\LogRecord;

class LaravelLogTracer
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

        foreach ($backtrace as $key => $trace) {
            if (!isset($trace['file'], $trace['line'])) {
                continue;
            }

            if (str_contains($trace['file'], '/vendor/')) {
                continue;
            }

            $classBasename = str($trace['file'])
                ->classBasename()
                ->remove('.php')
                ->value();
            $method = str($backtrace[$key + 1]['function'])
                ->whenContains('{closure', fn () => str("{closure:{$trace['line']}}"))
                ->value();

            break;
        }

        $newMessage = str(config('log-tracer.format'))
            ->replace('{{class_basename}}', $classBasename ?? 'unknown')
            ->replace('{{method}}', $method ?? 'unknown')
            ->replace('{{file}}', $trace['file'] ?? 'unknown')
            ->replace('{{line}}', $trace['line'] ?? '0')
            ->replace('{{message}}', $record->message)
            ->value();

        return $record->with(message: $newMessage);
    }
}

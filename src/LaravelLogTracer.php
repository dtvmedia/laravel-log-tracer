<?php

namespace Dtvmedia\LaravelLogTracer;

use Illuminate\Support\Str;
use Monolog\LogRecord;

class LaravelLogTracer
{
    public function __invoke(LogRecord $record): LogRecord
    {
        if (config('log-tracer.ignore_exceptions') && $this->isExceptionLog($record->message)) {
            return $record;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

        foreach ($backtrace as $key => $trace) {
            if (! isset($trace['file'], $trace['line'])) {
                continue;
            }

            if (str_contains($trace['file'], '/vendor/')) {
                continue;
            }

            $classBasename = Str::of($trace['file'])
                ->classBasename()
                ->remove('.php')
                ->value();
            $method = Str::of($backtrace[$key + 1]['function'])
                ->whenContains('{closure', fn () => Str::of("{closure:{$trace['line']}}"))
                ->value();

            break;
        }

        $newMessage = Str::of(config('log-tracer.format', '{{message}}'))
            ->replace('{{class_basename}}', $classBasename ?? 'unknown')
            ->replace('{{method}}', $method ?? 'unknown')
            ->replace('{{file}}', $trace['file'] ?? 'unknown')
            ->replace('{{line}}', (string) ($trace['line'] ?? '0'))
            ->replace('{{message}}', $record->message)
            ->value();

        return $record->with(message: $newMessage);
    }

    protected function isExceptionLog(string $logMessage): bool
    {
        $matchCount = 0;
        $matchCount += (int) preg_match('/\b(Exception|Error|Throwable)\b/i', $logMessage);
        $matchCount += (int) preg_match('/\.php:\d+/i', $logMessage);
        $matchCount += (int) (stripos($logMessage, '[stacktrace]') !== false);
        $matchCount += (int) (stripos($logMessage, 'Stack trace:') !== false);
        $matchCount += (int) preg_match('/#\d+ /', $logMessage);

        return $matchCount >= 2;
    }
}

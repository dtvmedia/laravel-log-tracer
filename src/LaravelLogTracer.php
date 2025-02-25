<?php

namespace Dtvmedia\LaravelLogTracer;

use Illuminate\Support\Str;
use Monolog\LogRecord;
use Throwable;

class LaravelLogTracer
{
    public function __invoke(LogRecord $record): LogRecord
    {
        try {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

            return $this->addSourceToLogRecord($record, $backtrace);
        } catch (Throwable) {
            return $record;
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $backtrace
     */
    protected function addSourceToLogRecord(LogRecord $record, array $backtrace): LogRecord
    {
        if (config('log-tracer.ignore_exceptions') && $this->isExceptionLog($record)) {
            return $record;
        }

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

    protected function isExceptionLog(LogRecord $record): bool
    {
        // check for real exceptions
        if (
            isset($record->context['exception'])
            && $record->context['exception'] instanceof Throwable
        ) {
            return true;
        }

        // check for exceptions in log message
        $logMessage = $record->message;
        $matchCount = 0;
        $matchCount += (int) preg_match('/\b(Exception|Error|Throwable)\b/i', $logMessage);
        $matchCount += (int) preg_match('/\.php:\d+/i', $logMessage);
        $matchCount += (int) (stripos($logMessage, '[stacktrace]') !== false);
        $matchCount += (int) (stripos($logMessage, 'Stack trace:') !== false);
        $matchCount += (int) preg_match('/#\d+ /', $logMessage);

        return $matchCount >= 2;
    }
}

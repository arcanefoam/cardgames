<?php

namespace App\Logic\AI;

use Illuminate\Support\Facades\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\Stringable;

class Logger implements LoggerInterface {

    public function emergency(string|\Stringable $message, array $context = array()): void {
        Log::emergency($message, $context);
    }

    public function alert(string|\Stringable $message, array $context = array()): void {
        Log::alert($message, $context);
    }

    public function critical(string|\Stringable $message, array $context = array()): void {
        Log::critical($message, $context);
    }

    public function error(string|\Stringable $message, array $context = array()): void {
        Log::error($message, $context);
    }

    public function warning(string|\Stringable $message, array $context = array()): void {
        Log::warning($message, $context);
    }

    public function notice(string|\Stringable $message, array $context = array()): void {
        Log::notice($message, $context);
    }

    public function info(string|\Stringable $message, array $context = array()): void {
        Log::info($message, $context);
    }

    public function debug(string|\Stringable $message, array $context = array()): void {
        Log::debug($message, $context);
    }

    public function log($level, string|\Stringable $message, array $context = array()): void {
        Log::log($level, $message, $context);
    }
}
<?php
declare(strict_types=1);

class RuntimeStats {
    private static $_totalQueries = 0;
    private static $_startTime = 0;

    public static function getTotalQueries(): int {
        return self::$_totalQueries;
    }

    public static function incrementTotalQueries() {
        ++self::$_totalQueries;
    }

    public static function startTimer() {
        self::$_startTime = microtime(true);
    }

    public static function getTimer(): float {
        return (microtime(true) - self::$_startTime);
    }
}

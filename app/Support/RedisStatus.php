<?php

namespace App\Support;

use Illuminate\Support\Facades\Redis;
use Throwable;

class RedisStatus
{
    private static ?bool $disponible = null;

    public static function disponible(): bool
    {
        if (self::$disponible !== null) {
            return self::$disponible;
        }

        try {
            $ping = Redis::ping();
            self::$disponible = is_bool($ping) ? $ping : ($ping === '+PONG');
        } catch (Throwable) {
            self::$disponible = false;
        }

        return self::$disponible;
    }
}

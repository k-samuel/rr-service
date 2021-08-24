<?php

declare(strict_types=1);

namespace KSamuel\RrService;

class Pinba
{
    /**
     * @var array | resource[] associative of pinba timers
     */
    private static array $timers;

    /**
     * enable\disable
     * @var bool
     */
    private static bool $enabled = true;

    /**
     * @param string $category
     * @param array<mixed,mixed> $data
     */
    public static function start(string $category, array $data = []): void
    {
        if (self::$enabled && function_exists('pinba_timer_start')) {
            $pinbaData = pinba_get_info();
            $tags = array(
                '__hostname' => $pinbaData['hostname'],
                '__server_name' => $pinbaData['server_name'],
                'category' => $category,
            );
            self::$timers[$category] = pinba_timer_start($tags, $data, 1);
        }
    }

    /**
     * @param string $category
     */
    public static function stop(string $category): void
    {
        if (self::$enabled && isset(self::$timers[$category]) && function_exists('pinba_timer_stop')) {
            pinba_timer_stop(self::$timers[$category]);
        }
    }

    /**
     * @param string $name
     */
    public static function setServer(string $name): void
    {
        if (self::$enabled && function_exists('pinba_server_name_set')) {
            pinba_server_name_set($name);
        }
    }

    /**
     * @param string $scriptName
     * @param int $flag
     */
    public static function flush(string $scriptName = '', ?int $flag = null): void
    {
        if (self::$enabled && function_exists('pinba_flush')) {
            if (is_null($flag)) {
                $flag = PINBA_FLUSH_RESET_DATA;
            }
            pinba_flush($scriptName, $flag);
        }
    }

    /**
     * @param bool $enabled
     */
    public static function setEnabled(bool $enabled): void
    {
        self::$enabled = $enabled;
    }
}

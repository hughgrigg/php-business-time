<?php

namespace BusinessTime;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateInterval;

/**
 * Add real unit access methods to the date interval class.
 *
 * Ensure correct type-hinting on these methods.
 *
 * @method static seconds(int $seconds = 1)
 * @method static minutes(int $minutes = 1)
 * @method static hour(int $hours = 1)
 * @method static hours(int $hours = 1)
 * @method static days(int $days = 1)
 */
class Interval extends CarbonInterval
{
    /**
     * @return float
     */
    public function inDays(): float
    {
        return $this->inHours() / 24;
    }

    /**
     * @return float
     */
    public function inHours(): float
    {
        return $this->inMinutes() / 60;
    }

    /**
     * @return float
     */
    public function inMinutes(): float
    {
        return $this->inSeconds() / 60;
    }

    /**
     * @return int
     */
    public function inSeconds(): int
    {
        return (new Carbon())->add($this)->diffInRealSeconds(new Carbon());
    }

    /**
     * @param DateInterval $interval
     *
     * @return float
     */
    public function asMultipleOf(DateInterval $interval): float
    {
        $interval = self::instance($interval);

        return $this->inSeconds() / $interval->inSeconds();
    }

    /**
     * Normalise human description to use sensible units. The default for
     * intervals is to use whichever arbitrary units they are specified in,
     * leading to e.g. "480 minutes" instead of "8 hours".
     *
     * @param bool $syntax
     * @param bool $short
     * @param int  $parts
     * @param null $options
     *
     * @throws \Exception
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function forHumans(
        $syntax = true,
        $short = false,
        $parts = 2,
        $options = null
    ): string {
        return (new Carbon())->add($this)->diffForHumans(null, $syntax, $short, $parts);
    }

    /**
     * Normalise instance creation to seconds.
     *
     * @param DateInterval $dateInterval
     * @param bool         $trimMicroseconds
     *
     * @return static
     */
    public static function instance(
        DateInterval $dateInterval,
        $trimMicroseconds = true
    ): self {
        return self::seconds(
            self::intervalToSeconds($dateInterval, $trimMicroseconds)
        );
    }

    /**
     * @param DateInterval $dateInterval
     * @param bool         $trimMicroseconds
     *
     * @return int
     */
    public static function intervalToSeconds(
        DateInterval $dateInterval,
        $trimMicroseconds = true
    ): int {
        $dateInterval->f =
            $trimMicroseconds ||
            version_compare(PHP_VERSION, '7.1.0-dev', '<') ? 0 :
                $dateInterval->f;

        return (new Carbon())->add($dateInterval)->diffInRealSeconds(
            new Carbon()
        );
    }
}

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
 * @method static seconds(int $seconds = null)
 * @method static minutes(int $minutes = null)
 * @method static hours(int $hours = null)
 * @method static days(int $days = null)
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
     * @return string
     */
    public function forHumans(): string
    {
        return (new Carbon())->add($this)->diffForHumans(null, true, false, 2);
    }

    /**
     * Normalise instance creation to seconds.
     *
     * @param DateInterval $dateInterval
     *
     * @return static
     */
    public static function instance(DateInterval $dateInterval): self
    {
        return self::seconds(self::intervalToSeconds($dateInterval));
    }

    /**
     * @param DateInterval $interval
     *
     * @return int
     */
    public static function intervalToSeconds(DateInterval $interval): int
    {
        return (new Carbon())->add($interval)->diffInRealSeconds(new Carbon());
    }
}

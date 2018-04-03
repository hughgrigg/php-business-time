<?php

namespace BusinessTime\Constraint;

use DateTime;
use DateTimeInterface;

/**
 * A business time constraint that matches times between certain times of day
 * as business time.
 *
 * e.g.
 * new BetweenTimesOfDay('8:30', '18:00') matches from 8:30am to 6pm.
 *
 * @see BetweenTimesOfDayTest
 */
class BetweenTimesOfDay extends RangeConstraint
{
    /**
     * @param string $min e.g. '09:00'
     * @param string $max e.g. '17:00'
     */
    public function __construct(string $min = '09:00', string $max = '17:00')
    {
        // Convert the given string times to their comparable minute of the day.
        parent::__construct(
            $this->minuteOfDay(new DateTime($min)),
            // Subtract one minute from the max to make it exclusive.
            $this->minuteOfDay(new DateTime($max)) - 1
        );
    }

    /**
     * Get an integer value from the time that is to be compared to this range.
     *
     * @param DateTimeInterface $time
     *
     * @return int
     */
    public function relevantValueOf(DateTimeInterface $time): int
    {
        return $this->minuteOfDay($time);
    }

    /**
     * @see BetweenTimesOfDayTest::testMinuteOfDay
     *
     * @param DateTimeInterface $time
     *
     * @return int
     */
    public function minuteOfDay(DateTimeInterface $time): int
    {
        return ($time->format('G') * 60) + (int) $time->format('i');
    }

    /**
     * Get the maximum possible value of the range.
     *
     * @return int
     */
    public function maxMax(): int
    {
        return 1440;
    }

    /**
     * Get the minimum possible value of the range.
     *
     * @return int
     */
    public function minMin(): int
    {
        return 0;
    }
}

<?php

namespace BusinessTime\Constraint;

use BusinessTime\BusinessTime;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;

/**
 * Constraint that matches business time between given days of the week,
 * inclusively.
 *
 * This follows ISO 8601, so Monday is the first day of the week and Sunday
 * is the last.
 * https://en.wikipedia.org/wiki/ISO_week_date
 *
 * e.g.
 * new BetweenDaysOfWeek('Monday', 'Friday') matches week days.
 * new BetweenDaysOfWeek('Monday', 'Saturday') matches week days and Saturday.
 * new BetweenDaysOfWeek('Monday', 'Sunday') matches any day.
 *
 * @see DaysOfWeek
 * @see WeekDays
 * @see BetweenDaysOfWeekTest
 */
class BetweenDaysOfWeek extends RangeConstraint
{
    /**
     * @param string|int $min e.g. 'Sunday' or 0
     * @param string|int $max e.g. 'Saturday' or 6
     */
    public function __construct(string $min, string $max)
    {
        // Convert named days of the week to numeric indexes.
        $min = $this->nameToIndex($min, DaysOfWeek::NAME_INDEX);
        $max = $this->nameToIndex($max, DaysOfWeek::NAME_INDEX);

        // Interpret passing the same day as min and max as meaning any day of
        // the week.
        if ($min === $max) {
            $min = $this->minMin();
            $max = $this->maxMax();
        }

        parent::__construct($min, $max);
    }

    /**
     * @param DateTimeInterface $time
     *
     * @return int
     */
    public function relevantValueOf(DateTimeInterface $time): int
    {
        return BusinessTime::fromDti($time)->dayOfWeekIso;
    }

    /**
     * Get the maximum possible value of the range.
     *
     * @return int
     */
    public function maxMax(): int
    {
        return 7;
    }

    /**
     * Get the minimum possible value of the range.
     *
     * @return int
     */
    public function minMin(): int
    {
        return 1;
    }
}

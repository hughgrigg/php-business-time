<?php

namespace BusinessTime\Constraint;

use Carbon\Carbon;
use DateTime;

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
 *
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
        if (!is_numeric($min)) {
            $min = DaysOfWeek::NAME_INDEX[$min];
        }
        if (!is_numeric($max)) {
            $max = DaysOfWeek::NAME_INDEX[$max];
        }

        // Keep the days within the valid range.
        $min = max((int) $min, 1);
        $max = min((int) $max, 7);

        // Allow backwards order.
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        // Interpret passing the same day as min and max as meaning any day of
        // the week.
        if ($min === $max) {
            $min = 1;
            $max = 7;
        }

        // It's more intuitive that "Monday to Friday" includes Friday, but the
        // default range logic is to treat the max exclusively, so we add 1.
        ++$max;

        parent::__construct($min, $max);
    }

    /**
     * @param DateTime $time
     *
     * @return int
     */
    public function relevantValueOf(DateTime $time): int
    {
        return Carbon::instance($time)->dayOfWeekIso;
    }
}

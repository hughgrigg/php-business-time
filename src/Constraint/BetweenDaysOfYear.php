<?php

namespace BusinessTime\Constraint;

use Carbon\Carbon;
use DateTime;
use DateTimeInterface;

/**
 * Business time constraint that matches times between certain days of the year.
 *
 * e.g
 * new BetweenDaysOfYear('10th May', '10th June') matches times from 10th May
 * to 10th June inclusively.
 *
 * @see BetweenDaysOfYearTest
 */
class BetweenDaysOfYear implements BusinessTimeConstraint
{
    /** @var string */
    private $minDayOfYear;

    /** @var string */
    private $maxDayOfYear;

    /**
     * @param string $minDayOfYear e.g. '10th May'
     * @param string $maxDayOfYear e.g. '10th June'
     */
    public function __construct(string $minDayOfYear, string $maxDayOfYear)
    {
        $this->minDayOfYear = $minDayOfYear;
        $this->maxDayOfYear = $maxDayOfYear;
    }

    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTimeInterface $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTimeInterface $time): bool
    {
        // Compare by month and day of month within the same year.
        // This allows leap years and non-leap years to be handled correctly,
        // as the index of the day of the year differs after 28th February
        // between leap years and non-leap years.
        $time         = Carbon::instance($time);
        $minDayOfYear = new Carbon("{$this->minDayOfYear} {$time->year}");
        $maxDayOfYear = new Carbon("{$this->maxDayOfYear} {$time->year}");

        // Beyond the min month and before the max month is a match regardless
        // of the day of the month.
        if ($time->month > $minDayOfYear->month
            && $time->month < $maxDayOfYear->month) {
            return true;
        }

        // If the min and max months are the same, then just compare the day
        // of the month.
        if ($time->month === $minDayOfYear->month
            && $time->month === $maxDayOfYear->month) {
            return $time->day >= $minDayOfYear->day
                   && $time->day <= $maxDayOfYear->day;
        }

        // If we're in the min month and past the min day, it's a match.
        if ($time->month === $minDayOfYear->month) {
            return $time->day >= $minDayOfYear->day;
        }

        // If we're in the max month and before the max day, it's a match.
        if ($time->month === $maxDayOfYear->month) {
            return $time->day <= $maxDayOfYear->day;
        }

        // Otherwise it's not a match.
        return false;
    }
}

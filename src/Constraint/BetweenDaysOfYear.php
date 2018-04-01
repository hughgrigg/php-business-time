<?php

namespace BusinessTime\Constraint;

use Carbon\Carbon;
use DateTime;

/**
 * Business time constraint that matches times between certain days of the year.
 *
 * e.g
 * new BetweenDaysOfYear('10th May', '10th June') matches times from 10th May
 * to 10th June inclusively.
 *
 * @see BetweenDaysOfYearTest
 */
class BetweenDaysOfYear extends RangeConstraint
{
    private const FORMAT       = 'z';
    public const  INDEX_FEB_29 = 59;

    /**
     * BetweenDaysOfYear constructor.
     *
     * @param string|int $min e.g. '10th May'
     * @param string|int $max e.g. '10th June'
     */
    public function __construct(string $min, string $max)
    {
        if (!is_numeric($min)) {
            $min = (new DateTime($min))->format(self::FORMAT);
        }
        if (!is_numeric($max)) {
            $max = (new DateTime($max))->format(self::FORMAT);
        }

        parent::__construct((int) $min, (int) $max);
    }

    /**
     * Get an integer value from the time that is to be compared to this range.
     *
     * @param DateTime $time
     *
     * @return int
     */
    public function relevantValueOf(DateTime $time): int
    {
        $dayOfYear = (int) $time->format(self::FORMAT);

        // Allow for leap years.
        $carbon = Carbon::instance($time);
        if ($carbon->dayOfYear > self::INDEX_FEB_29 && $carbon->isLeapYear()) {
            // Use the index - 1 to allow for the 29th February.
            return $dayOfYear - 1;
        }

        return $dayOfYear;
    }

    /**
     * Get the maximum possible value of the range.
     *
     * @return int
     */
    public function maxMax(): int
    {
        return 365; // = 31st December on a leap year, zero-indexed.
    }

    /**
     * Get the minimum possible value of the range.
     *
     * @return int
     */
    public function minMin(): int
    {
        return 0; // = 1st January.
    }
}

<?php

namespace BusinessTime\Constraint;

use DateTime;

/**
 * A business time constraint that matches times between certain months of the
 * year, inclusively.
 *
 * e.g.
 * new BetweenMonthsOfYear('February', 'December') matches any time except
 * January.
 *
 * @see BetweenMonthsOfYearTest
 */
class BetweenMonthsOfYear extends RangeConstraint
{
    /**
     * @param string|int $min e.g. "May" or 5
     * @param string|int $max e.g. "August" or 8
     */
    public function __construct(
        string $min = 'January',
        string $max = 'December'
    ) {
        // Convert named months of the year to numeric indexes.
        $min = $this->nameToIndex($min, MonthsOfYear::NAME_INDEX);
        $max = $this->nameToIndex($max, MonthsOfYear::NAME_INDEX);

        parent::__construct($min, $max);
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
        return (int) $time->format('n');
    }

    /**
     * Get the maximum possible value of the range.
     *
     * @return int
     */
    public function maxMax(): int
    {
        return 12;
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

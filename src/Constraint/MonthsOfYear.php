<?php

namespace BusinessTime\Constraint;

/**
 * Business time constraint that matches times in certain months of the year.
 *
 * e.g.
 * new MonthsOfYear('February', 'October') matches times in February or October.
 *
 * @see MonthsOfYearTest
 */
class MonthsOfYear extends FormatConstraint
{
    /**
     * @param string ...$monthsOfTheYear e.g. 'May', 'January'
     */
    public function __construct(string ...$monthsOfTheYear)
    {
        parent::__construct('F', ...$monthsOfTheYear);
    }
}

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
    const NAME_INDEX = [
        'January'   => 1,
        'Jan'       => 1,
        'February'  => 2,
        'Feb'       => 2,
        'March'     => 3,
        'Mar'       => 3,
        'April'     => 4,
        'Apr'       => 4,
        'May'       => 5,
        'June'      => 6,
        'Jun'       => 6,
        'July'      => 7,
        'Jul'       => 7,
        'August'    => 8,
        'Aug'       => 8,
        'September' => 9,
        'Sep'       => 9,
        'October'   => 10,
        'Oct'       => 10,
        'November'  => 11,
        'Nov'       => 11,
        'December'  => 12,
        'Dec'       => 12,
    ];

    /**
     * @param string ...$monthsOfTheYear e.g. 'May', 'January'
     */
    public function __construct(string ...$monthsOfTheYear)
    {
        parent::__construct('F', ...$monthsOfTheYear);
    }
}

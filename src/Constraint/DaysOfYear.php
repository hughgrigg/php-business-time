<?php

namespace BusinessTime\Constraint;

use DateTime;

/**
 * Business time constraint that matches specific days of the year.
 *
 * e.g.
 * new DaysOfYear('December 25th') matches 25th December only.
 * new DaysOfYear('December 25th', '1st April') matches 25th December and
 * April 1st.
 *
 * This constraint attempts to interpret various formats for the day of the year
 * using DateTime parsing, so most reasonable formats should work.
 *
 * @see DaysOfYearTest
 */
class DaysOfYear extends FormatConstraint
{
    private const FORMAT = 'F j';

    /**
     * @param string ...$daysOfYear e.g. "December 25th", "1st April"
     */
    public function __construct(string ...$daysOfYear)
    {
        parent::__construct(
            self::FORMAT,
            ...array_map(
                function (string $dayOfYear): string {
                    return (new DateTime($dayOfYear))->format(self::FORMAT);
                },
                $daysOfYear
            )
        );
    }
}

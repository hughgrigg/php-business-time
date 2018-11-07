<?php

namespace BusinessTime\Constraint;

use DateTime;

/**
 * Business time constraint that matches based on specific dates.
 *
 * e.g.
 * new Dates('2018-05-23') matches 23rd May 2018 only.
 *
 * This uses DateTime parsing, so it should work with any reasonable format for
 * the given dates.
 *
 * @see DatesTest
 */
class Dates extends FormatConstraint
{
    const FORMAT = 'Y-m-d';

    /**
     * @param string ...$dates e.g. '2018-05-23', '2019-04-01'
     */
    public function __construct(string ...$dates)
    {
        parent::__construct(
            self::FORMAT,
            ...array_map(
                function (string $date): string {
                    return (new DateTime($date))->format(self::FORMAT);
                },
                $dates
            )
        );
    }
}

<?php

namespace BusinessTime\Constraint;

use BusinessTime\Constraint\Composite\Combinations;
use DateTime;

/**
 * Constraint that matches any time as business time.
 */
class AnyTime implements BusinessTimeConstraint
{
    use Combinations;

    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTime $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTime $time): bool
    {
        return true;
    }
}

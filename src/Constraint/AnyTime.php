<?php

namespace BusinessTime\Constraint;

use BusinessTime\Constraint\Composite\Combinations;
use BusinessTime\Constraint\Narration\BasicNarration;
use BusinessTime\Constraint\Narration\BusinessTimeNarrator;
use DateTimeInterface;

/**
 * Constraint that matches any time as business time.
 *
 * Used as a null object pattern for business time constraints.
 */
class AnyTime implements BusinessTimeConstraint, BusinessTimeNarrator
{
    use Combinations;
    use BasicNarration;

    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTimeInterface $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTimeInterface $time): bool
    {
        return true;
    }
}

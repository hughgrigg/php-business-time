<?php

namespace BusinessTime\Constraint\Composite;

use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Tests\Unit\Constraint\Composite\CompositeNotTest;
use DateTimeInterface;

/**
 * A set of constraints that matches if none of the included constraints
 * matches. Could also be called "NotAny".
 *
 * This is equivalent to logical NOT.
 *
 * @see CompositeNotTest
 */
class Not implements BusinessTimeConstraint
{
    use Combinations;

    /** @var BusinessTimeConstraint[] */
    private $constraints;

    /**
     * @param BusinessTimeConstraint ...$constraints
     */
    public function __construct(BusinessTimeConstraint ...$constraints)
    {
        $this->constraints = $constraints;
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
        foreach ($this->constraints as $constraint) {
            if ($constraint->isBusinessTime($time)) {
                return false;
            }
        }

        return true;
    }
}

<?php

namespace BusinessTime\Constraint\Composite;

use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Tests\Unit\Constraint\Composite\CompositeAnyTest;
use DateTime;
use DateTimeInterface;

/**
 * A set of constraints that matches if any of the included constraints matches.
 *
 * This is equivalent to logical OR.
 *
 * @see CompositeAnyTest
 */
class Any implements BusinessTimeConstraint
{
    use Combinations;

    /** @var BusinessTimeConstraint[] */
    private $constraints;

    /**
     * @param BusinessTimeConstraint[] $constraints
     */
    public function __construct(BusinessTimeConstraint ...$constraints)
    {
        $this->constraints = $constraints;
    }

    /**
     * @param DateTimeInterface $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTimeInterface $time): bool
    {
        foreach ($this->constraints as $constraint) {
            if ($constraint->isBusinessTime($time)) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace BusinessTime\Constraint;

use DateTime;

/**
 * A set of constraints that matches if all of the included constraints match.
 *
 * This is equivalent to logical AND.
 */
class All implements BusinessTimeConstraint
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
     * @param DateTime $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTime $time): bool
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->isBusinessTime($time)) {
                return false;
            }
        }

        return true;
    }
}

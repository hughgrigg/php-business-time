<?php

namespace BusinessTime\Constraint;

use DateTime;

/**
 * A set of constraints that matches if none of the included constraints
 * matches.
 *
 * This is equivalent to logical NOT.
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
     * @param DateTime $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTime $time): bool
    {
        foreach ($this->constraints as $constraint) {
            if ($constraint->isBusinessTime($time)) {
                return false;
            }
        }

        return true;
    }
}

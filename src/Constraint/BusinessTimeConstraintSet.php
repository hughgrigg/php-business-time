<?php

namespace BusinessTime\Constraint;

use DateTime;

class BusinessTimeConstraintSet implements BusinessTimeConstraint
{
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

    /**
     * @param BusinessTimeConstraint ...$constraints
     *
     * @return BusinessTimeConstraintSet
     */
    public function addBusinessTimeConstraints(
        BusinessTimeConstraint ...$constraints
    ): self {
        return new self(array_merge($constraints, $this->constraints));
    }
}

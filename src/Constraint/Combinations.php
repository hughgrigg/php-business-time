<?php

namespace BusinessTime\Constraint;

/**
 * Combinatorial behaviour for constraints to make composites of themselves.
 */
trait Combinations
{
    /**
     * Get a composite constraint that matches if this constraint matches and
     * all of the additional constraints match.
     *
     * @param BusinessTimeConstraint ...$additional
     *
     * @return BusinessTimeConstraint|static
     */
    public function andAlso(
        BusinessTimeConstraint ...$additional
    ): BusinessTimeConstraint {
        return new All($this, ...$additional);
    }

    /**
     * Get a composite constraint that matches if this constraint matches if
     * this or any of the alternative constraints matches.
     *
     * @param BusinessTimeConstraint ...$alternatives
     *
     * @return BusinessTimeConstraint|static
     */
    public function orAlternatively(
        BusinessTimeConstraint ...$alternatives
    ): BusinessTimeConstraint {
        return new Any($this, ...$alternatives);
    }

    /**
     * Get a composite constraint that matches if this constraint matches and
     * none of the exceptional constraints matches.
     *
     * @param BusinessTimeConstraint ...$exceptions
     *
     * @return BusinessTimeConstraint|static
     */
    public function except(
        BusinessTimeConstraint ...$exceptions
    ): BusinessTimeConstraint {
        return new All($this, new Not(...$exceptions));
    }
}

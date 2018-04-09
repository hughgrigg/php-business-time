<?php

namespace BusinessTime\Constraint\Narration;

use BusinessTime\Constraint\AnyTime;
use BusinessTime\Constraint\BusinessTimeConstraint;
use DateTimeInterface;

/**
 * Decorator for business time constraints that ensures they offer business
 * time narration.
 *
 * If the decorated constraint implements narration, then that is used.
 * Otherwise default narration is provided.
 */
class DefaultNarrator implements BusinessTimeNarrator
{
    /** @var BusinessTimeConstraint */
    private $constraint;

    /**
     * @param BusinessTimeConstraint $constraint
     */
    public function __construct(BusinessTimeConstraint $constraint)
    {
        $this->constraint = $constraint;
    }

    /**
     * Get a business-relevant description for the given time.
     *
     * @param DateTimeInterface $time
     *
     * @return string
     */
    public function narrate(DateTimeInterface $time): string
    {
        if ($this->constraint instanceof BusinessTimeNarrator) {
            return $this->constraint->narrate($time);
        }

        return (new AnyTime())->narrate($time);
    }
}

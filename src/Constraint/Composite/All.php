<?php

namespace BusinessTime\Constraint\Composite;

use ArrayIterator;
use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Tests\Unit\Constraint\Composite\CompositeAllTest;
use DateTimeInterface;
use IteratorAggregate;
use Traversable;

/**
 * A set of constraints that matches if all of the included constraints match.
 *
 * This is equivalent to logical AND.
 *
 * @see CompositeAllTest
 */
class All implements BusinessTimeConstraint, IteratorAggregate
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
            if (!$constraint->isBusinessTime($time)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieve an external iterator.
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable
     * @since 5.0.0
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->constraints);
    }
}

<?php

namespace BusinessTime\Tests\Unit\Constraint\Composite;

use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\NoTime;
use PHPUnit\Framework\TestCase;

/**
 * Test the orAlternatively() combination method on business time constraints.
 *
 * @see \BusinessTime\Constraint\Composite\Combinations
 */
class OrAlternativelyCombinationTest extends TestCase
{
    use CompositeConstraintProviders;

    /**
     * A composite constraint from orAlternatively() should match when all the
     * component constraints match.
     *
     * @dataProvider allMatchProvider
     *
     * @param BusinessTimeConstraint[] $alternatives
     */
    public function testOrAlternativelyAllMatch(array $alternatives)
    {
        // Given we have a constraint which does not match a business time;
        $time = $this->wednesdayOnePm();
        $constraint = new NoTime();
        self::assertFalse($constraint->isBusinessTime($time));

        // When we use orAlternatively() to combine it with other constraints
        // that all match.
        $composite = $constraint->orAlternatively(...$alternatives);

        // Then the composite constraint should match the time.
        self::assertTrue(
            $composite->isBusinessTime($time),
            'The composite constraint should match the time.'
        );
    }

    /**
     * A composite constraint from orAlternatively() should not match when
     * none of the component constraints match.
     *
     * @dataProvider noneMatchProvider
     *
     * @param BusinessTimeConstraint[] $alternatives
     */
    public function testOrAlternativelyNoneMatch(array $alternatives)
    {
        // Given we have a constraint which does not match a business time;
        $time = $this->wednesdayOnePm();
        $constraint = new NoTime();
        self::assertFalse($constraint->isBusinessTime($time));

        // When we use orAlternatively() to combine it with other constraints
        // none of which match.
        $composite = $constraint->orAlternatively(...$alternatives);

        // Then the composite constraint should not match the time.
        self::assertFalse(
            $composite->isBusinessTime($time),
            'The composite constraint should not match the time.'
        );
    }

    /**
     * A composite constraint from orAlternatively() should match when only some
     * of the component constraints match.
     *
     * @dataProvider someMatchProvider
     *
     * @param BusinessTimeConstraint[] $alternatives
     */
    public function testOrAlternativelySomeMatch(array $alternatives)
    {
        // Given we have a constraint which does not match a business time;
        $time = $this->wednesdayOnePm();
        $constraint = new NoTime();
        self::assertFalse($constraint->isBusinessTime($time));

        // When we use orAlternatively() to combine it with other constraints
        // only some of which match.
        $composite = $constraint->orAlternatively(...$alternatives);

        // Then the composite constraint should match the time.
        self::assertTrue(
            $composite->isBusinessTime($time),
            'The composite constraint should match the time.'
        );
    }
}

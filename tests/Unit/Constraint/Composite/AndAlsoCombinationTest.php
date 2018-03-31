<?php

namespace BusinessTime\Tests\Unit\Constraint\Composite;

use BusinessTime\Constraint\AnyTime;
use BusinessTime\Constraint\BusinessTimeConstraint;
use PHPUnit\Framework\TestCase;

/**
 * Test the andAlso() combination method on business time constraints.
 *
 * @see \BusinessTime\Constraint\Composite\Combinations
 */
class AndAlsoCombinationTest extends TestCase
{
    use CompositeConstraintProviders;

    /**
     * A composite constraint from andAlso() should match when all the component
     * constraints match.
     *
     * @dataProvider allMatchProvider
     *
     * @param BusinessTimeConstraint[] $additional
     */
    public function testAndAlsoAllMatch(array $additional): void
    {
        // Given we have a constraint which matches a business time;
        $time = $this->wednesdayOnePm();
        $constraint = new AnyTime();
        self::assertTrue($constraint->isBusinessTime($time));

        // When we use andAlso() to combine it with other constraints that all
        // match.
        $composite = $constraint->andAlso(...$additional);

        // Then the composite constraint should also match the time.
        self::assertTrue(
            $composite->isBusinessTime($time),
            'The composite constraint should match the time.'
        );
    }

    /**
     * A composite constraint from andAlso() should not match when none of the
     * component constraints match.
     *
     * @dataProvider noneMatchProvider
     *
     * @param BusinessTimeConstraint[] $additional
     */
    public function testAndAlsoNoneMatch(array $additional): void
    {
        // Given we have a constraint which matches a business time;
        $time = $this->wednesdayOnePm();
        $constraint = new AnyTime();
        self::assertTrue($constraint->isBusinessTime($time));

        // When we use andAlso() to combine it with other constraints none of
        // which match.
        $composite = $constraint->andAlso(...$additional);

        // Then the composite constraint should not match the time.
        self::assertFalse(
            $composite->isBusinessTime($time),
            'The composite constraint should not match the time.'
        );
    }

    /**
     * A composite constraint from andAlso() should not match when only some of
     * the component constraints match.
     *
     * @dataProvider someMatchProvider
     *
     * @param BusinessTimeConstraint[] $additional
     */
    public function testAndAlsoSomeMatch(array $additional): void
    {
        // Given we have a constraint which matches a business time;
        $time = $this->wednesdayOnePm();
        $constraint = new AnyTime();
        self::assertTrue($constraint->isBusinessTime($time));

        // When we use andAlso() to combine it with other constraints only
        // some of which match.
        $composite = $constraint->andAlso(...$additional);

        // Then the composite constraint should not match the time.
        self::assertFalse(
            $composite->isBusinessTime($time),
            'The composite constraint should not match the time.'
        );
    }
}

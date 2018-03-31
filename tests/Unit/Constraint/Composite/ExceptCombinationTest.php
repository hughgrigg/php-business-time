<?php

namespace BusinessTime\Tests\Unit\Constraint\Composite;

use BusinessTime\Constraint\AnyTime;
use BusinessTime\Constraint\BusinessTimeConstraint;
use PHPUnit\Framework\TestCase;

/**
 * Test the except() combination method on business time constraints.
 *
 * @see \BusinessTime\Constraint\Composite\Combinations
 */
class ExceptCombinationTest extends TestCase
{
    use CompositeConstraintProviders;

    /**
     * A composite constraint from except() should not match when all the new
     * component constraints match.
     *
     * @dataProvider allMatchProvider
     *
     * @param BusinessTimeConstraint[] $exceptions
     */
    public function testExceptAllMatch(array $exceptions): void
    {
        // Given we have a constraint which matches a business time;
        $time = $this->wednesdayOnePm();
        $constraint = new AnyTime();
        self::assertTrue($constraint->isBusinessTime($time));

        // When we use except() to combine it with other constraints that all
        // match.
        $composite = $constraint->except(...$exceptions);

        // Then the composite constraint should not match the time.
        self::assertFalse(
            $composite->isBusinessTime($time),
            'The composite constraint should not match the time.'
        );
    }

    /**
     * A composite constraint from except() should match when none of the new
     * component constraints match.
     *
     * @dataProvider noneMatchProvider
     *
     * @param BusinessTimeConstraint[] $exceptions
     */
    public function testExceptNoneMatch(array $exceptions): void
    {
        // Given we have a constraint which matches a business time;
        $time = $this->wednesdayOnePm();
        $constraint = new AnyTime();
        self::assertTrue($constraint->isBusinessTime($time));

        // When we use except() to combine it with other constraints none of
        // which match.
        $composite = $constraint->except(...$exceptions);

        // Then the composite constraint should match the time.
        self::assertTrue(
            $composite->isBusinessTime($time),
            'The composite constraint should match the time.'
        );
    }

    /**
     * A composite constraint from except() should not match when some of the
     * new component constraints match.
     *
     * @dataProvider someMatchProvider
     *
     * @param BusinessTimeConstraint[] $exceptions
     */
    public function testExceptSomeMatch(array $exceptions): void
    {
        // Given we have a constraint which matches a business time;
        $time = $this->wednesdayOnePm();
        $constraint = new AnyTime();
        self::assertTrue($constraint->isBusinessTime($time));

        // When we use except() to combine it with other constraints some of
        // which match.
        $composite = $constraint->except(...$exceptions);

        // Then the composite constraint should not match the time.
        self::assertFalse(
            $composite->isBusinessTime($time),
            'The composite constraint should not match the time.'
        );
    }
}

<?php

namespace BusinessTime\Tests\Unit\Constraint\Composite;

use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\Composite\Not;
use PHPUnit\Framework\TestCase;

/**
 * Test the Not composite constraint.
 */
class CompositeNotTest extends TestCase
{
    use CompositeConstraintProviders;

    /**
     * A Not composite constraint should not match if all of its component
     * constraints match.
     *
     * @dataProvider allMatchProvider
     *
     * @param BusinessTimeConstraint[] $constraints
     */
    public function testAllMatch(array $constraints): void
    {
        // Given we have a set of constraints;

        // And they all match a time as business time;
        $time = $this->wednesdayOnePm();
        foreach ($constraints as $constraint) {
            self::assertTrue($constraint->isBusinessTime($time));
        }

        // When we make a composite Not constraint with them;
        $not = new Not(...$constraints);

        // Then it should not match that time as business time.
        self::assertFalse(
            $not->isBusinessTime($time),
            'The Not composite constraint should not match the time.'
        );
    }

    /**
     * A Not composite constraint should match if none of its component
     * constraints match.
     *
     * @dataProvider noneMatchProvider
     *
     * @param BusinessTimeConstraint[] $constraints
     */
    public function testNoneMatch(array $constraints): void
    {
        // Given we have a set of constraints;

        // And none of them match a time as business time;
        $time = $this->wednesdayOnePm();
        foreach ($constraints as $constraint) {
            self::assertFalse(
                $constraint->isBusinessTime($time),
                'The constraint should not match.'
            );
        }

        // When we make a composite Not constraint with them;
        $not = new Not(...$constraints);

        // Then it should match that time as business time.
        self::assertTrue(
            $not->isBusinessTime($time),
            'The Not composite constraint should match the time.'
        );
    }

    /**
     * A Not composite constraint should not match if some of its component
     * constraints match.
     *
     * @dataProvider someMatchProvider
     *
     * @param BusinessTimeConstraint[] $constraints
     */
    public function testSomeMatch(array $constraints): void
    {
        // Given we have a set of constraints;

        // And some of them match a time as business time but some don't;
        $time = $this->wednesdayOnePm();
        $someMatch = false;
        $someDoNotMatch = false;
        foreach ($constraints as $constraint) {
            if ($constraint->isBusinessTime($time)) {
                $someMatch = true;
            } else {
                $someDoNotMatch = true;
            }
        }
        self::assertTrue(
            $someMatch,
            'Some of the constraints should match the time.'
        );
        self::assertTrue(
            $someDoNotMatch,
            'Some of the constraints should not match the time.'
        );

        // When we make a composite Not constraint with them;
        $not = new Not(...$constraints);

        // Then it should not match that time as business time.
        self::assertFalse(
            $not->isBusinessTime($time),
            'The Not composite constraint should not match the time.'
        );
    }
}

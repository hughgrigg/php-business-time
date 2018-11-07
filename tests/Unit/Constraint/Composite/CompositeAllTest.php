<?php

namespace BusinessTime\Tests\Unit\Constraint\Composite;

use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\Composite\All;
use PHPUnit\Framework\TestCase;

/**
 * Test the All composite constraint.
 */
class CompositeAllTest extends TestCase
{
    use CompositeConstraintProviders;

    /**
     * An All composite constraint should match if all of its component
     * constraints match.
     *
     * @dataProvider allMatchProvider
     *
     * @param BusinessTimeConstraint[] $constraints
     */
    public function testAllMatch(array $constraints)
    {
        // Given we have a set of constraints;

        // And they all match a time as business time;
        $time = $this->wednesdayOnePm();
        foreach ($constraints as $constraint) {
            self::assertTrue($constraint->isBusinessTime($time));
        }

        // When we make a composite All constraint with them;
        $all = new All(...$constraints);

        // Then it should also match that time as business time.
        self::assertTrue(
            $all->isBusinessTime($time),
            'The All composite constraint should match the time.'
        );
    }

    /**
     * An All composite constraint should not match if none of its component
     * constraints match.
     *
     * @dataProvider noneMatchProvider
     *
     * @param BusinessTimeConstraint[] $constraints
     */
    public function testNoneMatch(array $constraints)
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

        // When we make a composite All constraint with them;
        $all = new All(...$constraints);

        // Then it should also not match that time as business time.
        self::assertFalse(
            $all->isBusinessTime($time),
            'The All composite constraint should not match the time.'
        );
    }

    /**
     * An All composite constraint should not match if only some of its
     * component constraints match.
     *
     * @dataProvider someMatchProvider
     *
     * @param BusinessTimeConstraint[] $constraints
     */
    public function testSomeMatch(array $constraints)
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

        // When we make a composite All constraint with them;
        $all = new All(...$constraints);

        // Then it should not match that time as business time.
        self::assertFalse(
            $all->isBusinessTime($time),
            'The All composite constraint should not match the time.'
        );
    }
}

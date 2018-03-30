<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\Weekends;
use PHPUnit\Framework\TestCase;

/**
 * Test the Weekends business time constraint.
 */
class WeekendsTest extends TestCase
{
    /**
     * @dataProvider weekendsProvider
     *
     * @param string $time
     * @param bool   $shouldMatch
     */
    public function testWeekends(string $time, bool $shouldMatch): void
    {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for weekends
        $constraint = new Weekends();

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * @return array[]
     */
    public function weekendsProvider(): array
    {
        return [
            ['Monday', false],
            ['Tuesday', false],
            ['Wednesday', false],
            ['Thursday', false],
            ['Friday', false],
            ['Saturday', true],
            ['Sunday', true],
            ['2018-05-23', false],
            ['2018-05-26', true],
            ['2018-05-27', true],
        ];
    }
}
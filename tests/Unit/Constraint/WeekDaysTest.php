<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\WeekDays;
use PHPUnit\Framework\TestCase;

/**
 * Test the WeekDays business time constraint.
 */
class WeekDaysTest extends TestCase
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

        // And a constraint for week days;
        $constraint = new WeekDays();

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
            ['Monday', true],
            ['Tuesday', true],
            ['Wednesday', true],
            ['Thursday', true],
            ['Friday', true],
            ['Saturday', false],
            ['Sunday', false],
            ['2018-05-23', true],
            ['2018-05-26', false],
            ['2018-05-27', false],
        ];
    }
}

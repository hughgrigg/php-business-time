<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BetweenDaysOfMonth;
use PHPUnit\Framework\TestCase;

/**
 * Test the BetweenDaysOfMonth business time constraint.
 */
class BetweenDaysOfMonthTest extends TestCase
{
    /**
     * @dataProvider betweenDaysOfMonthProvider
     *
     * @param int    $minDayOfMonth
     * @param int    $maxDayOfMonth
     * @param string $time
     * @param bool   $shouldMatch
     */
    public function testBetweenDaysOfMonth(
        int $minDayOfMonth,
        int $maxDayOfMonth,
        string $time,
        bool $shouldMatch
    ) {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint matching times between days of the month;
        $constraint = new BetweenDaysOfMonth($minDayOfMonth, $maxDayOfMonth);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides min and max indexed days of the month with times, and whether
     * they should be matched as business time accordingly.
     *
     * @return array[]
     */
    public function betweenDaysOfMonthProvider(): array
    {
        return [
            // Min Max Time        Match?
            [5, 8, '7th January', true],
            [8, 5, '7th January', true],
            [5, 8, '1st January', false],
            [5, 31, '31st January', true],
            [5, 31, '4th January', false],
            [29, 29, '29th February 2020', true],
            [25, 26, '2018-05-23', false],
            [23, 24, '2018-05-23', true],
            [22, 23, '2018-05-23', true],
            [22, 24, '2018-05-23', true],
            [1, 31, 'now', true],
        ];
    }
}

<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\DaysOfMonth;
use PHPUnit\Framework\TestCase;

/**
 * Test the DaysOfMonth business time constraint.
 */
class DaysOfMonthTest extends TestCase
{
    /**
     * @dataProvider daysOfMonthProvider
     *
     * @param string $time
     * @param array  $daysOfMonth
     * @param bool   $shouldMatch
     */
    public function testDaysOfMonth(
        string $time,
        array $daysOfMonth,
        bool $shouldMatch
    ): void {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for certain days of the month;
        $constraint = new DaysOfMonth(...$daysOfMonth);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides times with a set of indexed days of the month, and whether the
     * time should be matched as business time accordingly.
     *
     * @return array[]
     */
    public function daysOfMonthProvider(): array
    {
        return [
            // Time         Days Match?
            ['1st January', [1], true],
            ['2nd January', [1], false],
            ['2nd January', [2], true],
            ['2nd January', [1, 2], true],
            ['31st January', [1, 8, 23, 31], true],
            ['29th February 2020', [29], true],
        ];
    }
}

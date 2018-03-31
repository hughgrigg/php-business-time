<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\MonthsOfYear;
use PHPUnit\Framework\TestCase;

/**
 * Test the MonthsOfYear business time constraint.
 */
class MonthsOfYearTest extends TestCase
{
    /**
     * @dataProvider monthsOfYearProvider
     *
     * @param string   $time
     * @param string[] $monthsOfYear
     * @param bool     $shouldMatch
     */
    public function testMonthsOfYear(
        string $time,
        array $monthsOfYear,
        bool $shouldMatch
    ): void {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for certain months of the year;
        $constraint = new MonthsOfYear(...$monthsOfYear);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides times with sets of months of the year, and whether the time
     * should be matched as business time accordingly.
     *
     * @return array[]
     */
    public function monthsOfYearProvider(): array
    {
        return [
            // Time Months Match?
            ['January 8th', ['January'], true],
            ['January 8th', ['February'], false],
            ['February 14th', ['January'], false],
            ['January 8th', ['January', 'February'], true],
            ['March 17th', ['March', 'July', 'January'], true],
            ['July 31st', ['March', 'July', 'January'], true],
            ['January 5th', ['March', 'July', 'January'], true],
            ['April 1st', ['March', 'July', 'January'], false],
        ];
    }
}

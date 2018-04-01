<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BetweenMonthsOfYear;
use PHPUnit\Framework\TestCase;

/**
 * Test the BetweenMonthsOfYear business time constraint.
 */
class BetweenMonthsOfYearTest extends TestCase
{
    /**
     * @dataProvider betweenTimesOfYearProvider
     *
     * @param string $time
     * @param string $minMonthOfYear
     * @param string $maxMonthOfYear
     * @param bool   $shouldMatch
     */
    public function testBetweenTimesOfYear(
        string $time,
        string $minMonthOfYear,
        string $maxMonthOfYear,
        bool $shouldMatch
    ): void {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint matching times between days of the week;
        $constraint = new BetweenMonthsOfYear($minMonthOfYear, $maxMonthOfYear);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides times with min and max months of the year, and whether the time
     * should be matched as business time accordingly.
     *
     * @return array[]
     */
    public function betweenTimesOfYearProvider(): array
    {
        return [
            // Time          Min         Max        Match?
            ['January 8th', 'January', 'February', true],
            ['February 14th', 'January', 'February', true],
            ['March 12th', 'January', 'February', false],
            ['January 8th', 'February', 'March', false],
            ['January 8th', 'February', 'November', false],
            ['December 1st', 'January', 'February', false],
            ['January 8th', 'January', 'February', true],
            ['February 29th 2020', 'January', 'February', true],
            ['now', 'January', 'December', true],
        ];
    }
}

<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BetweenDaysOfYear;
use PHPUnit\Framework\TestCase;

/**
 * Test the BetweenDaysOfYear business time constraint.
 */
class BetweenDaysOfYearTest extends TestCase
{
    /**
     * @dataProvider betweenDaysOfYearProvider
     *
     * @param string $time
     * @param string $minDayOfYear
     * @param string $maxDayOfYear
     * @param bool   $shouldMatch
     */
    public function testBetweenDaysOfYear(
        string $time,
        string $minDayOfYear,
        string $maxDayOfYear,
        bool $shouldMatch
    ): void {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint matching times between days of the week;
        $constraint = new BetweenDaysOfYear($minDayOfYear, $maxDayOfYear);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides times with min and max days of the year and whether the time
     * should be matched as business time accordingly.
     *
     * @return array[]
     */
    public function betweenDaysOfYearProvider(): array
    {
        return [
            // Time Min Max Match?
            ['2018-01-01', '1st January', '2nd January', true],
            ['2018-01-02', '1st January', '2nd January', true],
            ['2018-02-01', '1st January', '31st January', false],
            ['2018-05-09', '10th May', '10th June', false],
            ['2018-05-10', '10th May', '10th June', true],
            ['2018-05-11', '10th May', '10th June', true],
            ['2018-06-09', '10th May', '10th June', true],
            ['2018-06-10', '10th May', '10th June', true],
            ['2018-06-11', '10th May', '10th June', false],
            ['2020-02-29', '1st February', '1st March', true],
            ['2020-02-29', '29th February', '29th February', true],
            ['2020-01-01', '1st January', '31st December', true],
            ['2020-02-29', '1st January', '31st December', true],
            ['2020-03-01', '1st January', '31st December', true],
            ['2020-12-31', '1st January', '31st December', true],
            ['2019-02-28', '29th February', '29th February', false],
            ['2018-02-28', '29th February', '2nd March', false],
            ['2018-03-01', '29th February', '2nd March', true],
            ['now', '1st January', '31st December', true],
        ];
    }
}

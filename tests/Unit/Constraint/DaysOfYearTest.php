<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\DaysOfYear;
use PHPUnit\Framework\TestCase;

/**
 * Test the DaysOfYear business time constraint.
 */
class DaysOfYearTest extends TestCase
{
    /**
     * @dataProvider daysOfYearProvider
     *
     * @param string   $time
     * @param string[] $daysOfYear
     * @param bool     $shouldMatch
     */
    public function testDaysOfYear(
        string $time,
        array $daysOfYear,
        bool $shouldMatch
    ) {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for certain days of the year;
        $constraint = new DaysOfYear(...$daysOfYear);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides times with sets of days of the year, and whether the time should
     * be matched as business time accordingly.
     *
     * @return array[]
     */
    public function daysOfYearProvider(): array
    {
        return [
            // Time        Days of year     Match?
            ['2018-01-01', ['January 1st'], true],
            ['2018-01-01', ['1st January'], true],
            ['2018-01-01', ['January 01'], true],
            ['2018-01-01', ['1st Jan'], true],
            ['2018-01-02', ['1st January'], false],
            ['2018-02-01', ['1st January'], false],
            ['2018-01-02', ['1st January', '2nd January'], true],
            ['2018-02-01', ['1st January', '1st February'], true],
        ];
    }
}

<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\Dates;
use PHPUnit\Framework\TestCase;

/**
 * Test the Dates business time constraint.
 */
class DatesTest extends TestCase
{
    /**
     * @dataProvider datesProvider
     *
     * @param string   $time
     * @param string[] $dates
     * @param bool     $shouldMatch
     */
    public function testDates(
        string $time,
        array $dates,
        bool $shouldMatch
    ): void {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for certain dates;
        $constraint = new Dates(...$dates);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides times with sets of dates and whether the time should be matched
     * as business time accordingly.
     *
     * @return array[]
     */
    public function datesProvider(): array
    {
        return [
            // Time              Dates           Match?
            ['January 1st 2018', ['2018-01-01'], true],
            ['January 1st 2018', ['2018-01-02'], false],
            ['January 1st 2018', ['2018-02-01'], false],
            ['January 1st 2018', ['2019-01-01'], false],
            ['January 2nd 2018', ['2018-01-01'], false],
            ['February 1st 2018', ['2018-02-01'], true],
            ['January 1st 2019', ['2019-01-01'], true],
            ['January 1st 2018', ['2018-01-01', '2018-05-23'], true],
            ['January 1st 2018', ['2018-01-02', '2018-01-01'], true],
            ['January 1st 2018', ['2018-02-01', '2018-01-01'], true],
            ['January 1st 2018', ['2019-01-01', '2018-01-01'], true],
            ['January 2nd 2018', ['2018-01-01', '2018-01-02'], true],
            ['February 1st 2018', ['2018-02-01', '2019-02-01'], true],
            ['January 1st 2019', ['2019-01-01', '2019-02-01'], true],
            ['February 29th 2020', ['2020-02-29'], true],
            ['February 29th 2020', ['2020-03-01'], false],
        ];
    }
}

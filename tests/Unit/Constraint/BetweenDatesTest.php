<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BetweenDates;
use PHPUnit\Framework\TestCase;

/**
 * Test the BetweenDates business time constraint.
 */
class BetweenDatesTest extends TestCase
{
    /**
     * @dataProvider betweenDatesProvider
     *
     * @param string $time
     * @param string $minDate
     * @param string $maxDate
     * @param bool   $shouldMatch
     */
    public function testBetweenDates(
        string $time,
        string $minDate,
        string $maxDate,
        bool $shouldMatch
    ): void {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint matching times between certain dates;
        $constraint = new BetweenDates($minDate, $maxDate);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides times with min and max dates and whether the time should be
     * matched as business time accordingly.
     *
     * @return array[]
     */
    public function betweenDatesProvider(): array
    {
        return [
            // Time         Min           Max          Match?
            ['2018-05-23', '2018-05-22', '2018-05-24', true],
            ['2018-05-23', '2018-05-23', '2018-05-24', true],
            ['2018-05-23', '2018-05-22', '2018-05-23', true],
            ['2018-05-23', '2018-05-23', '2018-05-23', true],
            ['2018-05-23', '2018-05-24', '2018-05-25', false],
            ['2018-05-23', '2018-05-21', '2018-05-22', false],
            ['2019-05-23', '2018-05-20', '2018-05-30', false],
            ['2018-05-23', '2019-05-20', '2019-05-30', false],
            ['2017-05-23', '2018-05-20', '2018-05-30', false],
            ['2020-02-29', '2020-02-01', '2020-03-01', true],
        ];
    }
}

<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\HoursOfDay;
use PHPUnit\Framework\TestCase;

/**
 * Test the HoursOfDay business time constraint.
 */
class HoursOfDayTest extends TestCase
{
    /**
     * @dataProvider hoursOfDayProvider
     *
     * @param string $time
     * @param int[]  $hoursOfDay
     * @param bool   $shouldMatch
     */
    public function testHoursOfDay(
        string $time,
        array $hoursOfDay,
        bool $shouldMatch
    ): void {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for certain hours of the day;
        $constraint = new HoursOfDay(...$hoursOfDay);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides times with sets of days of the week, and whether the time should
     * be matched as business time based on those.
     *
     * @return array[]
     */
    public function hoursOfDayProvider(): array
    {
        return [
            // Time Hours of day Match?
            ['1pm', [13], true],
            ['1pm', [12, 14], false],
            ['8am', [8, 3, 19], true],
            ['now', range(0, 23), true],
        ];
    }
}

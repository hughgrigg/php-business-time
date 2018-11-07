<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\DaysOfWeek;
use PHPUnit\Framework\TestCase;

/**
 * Test the DaysOfWeek business time constraint.
 */
class DaysOfWeekTest extends TestCase
{
    /**
     * @dataProvider daysOfWeekProvider
     *
     * @param string   $time
     * @param string[] $daysOfWeek
     * @param bool     $shouldMatch
     */
    public function testDaysOfWeek(
        string $time,
        array $daysOfWeek,
        bool $shouldMatch
    ) {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for certain days of the week;
        $constraint = new DaysOfWeek(...$daysOfWeek);

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
    public function daysOfWeekProvider(): array
    {
        return [
            // Time       Days of week   Match?
            ['Wednesday', ['Wednesday'], true],
            ['Wednesday', ['Tuesday'], false],
            ['Wednesday', ['Tuesday', 'Thursday'], false],
        ];
    }
}

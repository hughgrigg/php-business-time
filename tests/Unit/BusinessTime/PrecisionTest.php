<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use BusinessTime\Interval;
use DateInterval;
use PHPUnit\Framework\TestCase;

/**
 * Test functionality around the precision of business time calculation.
 */
class PrecisionTest extends TestCase
{
    /**
     * The default precision should be 1 hour.
     */
    public function testPrecisionDefault()
    {
        // Given we have a business time with the default behaviour;
        $time = new BusinessTime();

        // Then the precision should be 1 hour.
        self::assertEqualsWithDelta(
            1.0,
            $time->precision()->inHours(),
            2,
            'Should be 1 hour',
        );
    }

    /**
     * Should be able to set the precision.
     *
     * @dataProvider setPrecisionProvider
     *
     * @param DateInterval $precision
     * @param int          $expectedSeconds
     */
    public function testSetPrecision(
        DateInterval $precision,
        int $expectedSeconds
    ) {
        // Given we have a business time instance;
        $time = new BusinessTime();

        // When we set the precision;
        $time->setPrecision($precision);

        // Then the precision should be as expected.
        self::assertEqualsWithDelta(
            $expectedSeconds,
            $time->precision()->inSeconds(),
            2,
            "Should be $expectedSeconds seconds",
        );
    }

    /**
     * @return array[]
     */
    public function setPrecisionProvider(): array
    {
        return [
            [Interval::second(), 1],
            [Interval::minute(), 60],
            [Interval::seconds(90), 90],
            [Interval::hour(), 3600],
        ];
    }

    /**
     * Demonstrate errors introduced by obtuse precision and how they are fixed
     * with finer precision.
     *
     * @dataProvider precisionOffByOneExamplesProvider
     *
     * @param string       $time
     * @param string       $otherTime
     * @param DateInterval $precision
     * @param float        $expectedHoursDiff
     */
    public function testPrecisionExamples(
        string $time,
        string $otherTime,
        DateInterval $precision,
        float $expectedHoursDiff
    ) {
        // Given we have a business time instance for a particular time;
        $businessTime = new BusinessTime($time);

        // And we set the precision;
        $businessTime->setPrecision($precision);

        // When we get diff with another time;
        $diff = $businessTime->diffInPartialBusinessHours(
            new BusinessTime($otherTime)
        );

        // Then we should get the expected diff.
        self::assertEqualsWithDelta($expectedHoursDiff, $diff, 0.01);
    }

    /**
     * Provides times with a precision and the "expected" diff in partial hours
     * based on those. This demonstrates how the precision can affect the
     * correctness of diff results.
     *
     * Whilst these errors can be fixed by setting finer precision intervals,
     * there is a performance trade-off in doing so as more iterations occur the
     * finer the precision is.
     *
     * @return array[]
     */
    public function precisionOffByOneExamplesProvider(): array
    {
        return [
            ['Monday 09:00', 'Monday 09:30', Interval::hour(), 1.0],
            ['Monday 09:00', 'Monday 09:30', Interval::minutes(30), 0.5],
            ['Monday 09:00', 'Monday 09:45', Interval::minutes(30), 1.0],
            ['Monday 09:00', 'Monday 09:45', Interval::minutes(15), 0.75],
            ['Monday 09:00', 'Monday 09:40', Interval::minutes(15), 0.75],
            ['Monday 09:00', 'Monday 09:40', Interval::minutes(5), 0.66],
            ['Monday 09:00', 'Monday 09:45', Interval::minutes(5), 0.75],
            ['Monday 09:00', 'Monday 09:41', Interval::minutes(5), 0.75],
            ['Monday 09:00', 'Monday 09:41', Interval::minutes(1), 0.68],
        ];
    }
}

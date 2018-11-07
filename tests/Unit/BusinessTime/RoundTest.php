<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use BusinessTime\Interval;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::floor(), BusinessTime::round() and
 * BusinessTime::ceil() methods.
 *
 * Note that these methods only make sense for units of time up to 1 day.
 * Beyond that the results become unintuitive because the rounding is done based
 * on seconds since the epoch, which won't match up with concepts like "the
 * current week" or "current month". Carbon has methods like startOfWeek,
 * endOfMonth etc that are more appropriate for that.
 */
class RoundTest extends TestCase
{
    /**
     * Test getting the flooring a time to various precision intervals.
     *
     * @dataProvider floorProvider
     *
     * @param string   $time
     * @param Interval $precision
     * @param string   $expectedFlooredTime
     */
    public function testFloor(
        string $time,
        Interval $precision = null,
        string $expectedFlooredTime = ''
    ) {
        // Given we have a business time instance for a specific time;
        $businessTime = new BusinessTime($time);

        // When we floor it to a precision interval.
        $floored = $businessTime->floor($precision);

        // Then we should get the expected floored time.
        self::assertSame(
            $expectedFlooredTime,
            $floored->format('Y-m-d H:i'),
            sprintf(
                '%s floored to %s should be %s; got %s.',
                $time,
                ($precision ?: $businessTime->precision())->forHumans(),
                $expectedFlooredTime,
                $floored->format('Y-m-d H:i')
            )
        );
    }

    /**
     * Provides times and the expected new time after flooring to various
     * precision intervals.
     *
     * @return array[]
     */
    public function floorProvider(): array
    {
        return [
            ['2018-05-23 17:23', null, '2018-05-23 17:00'], // default precision
            ['2018-05-23 17:23', Interval::minute(), '2018-05-23 17:23'],
            ['2018-05-23 17:23', Interval::minutes(10), '2018-05-23 17:20'],
            ['2018-05-23 17:23', Interval::minutes(15), '2018-05-23 17:15'],
            ['2018-05-23 17:23', Interval::minutes(30), '2018-05-23 17:00'],
            ['2018-05-23 17:23', Interval::hour(), '2018-05-23 17:00'],
            ['2018-05-23 17:23', Interval::hours(2), '2018-05-23 16:00'],
            ['2018-05-23 17:23', Interval::hours(3), '2018-05-23 15:00'],
            ['2018-05-23 17:23', Interval::hours(6), '2018-05-23 12:00'],
            ['2018-05-23 17:23', Interval::hours(8), '2018-05-23 16:00'],
            ['2018-05-23 17:23', Interval::hours(12), '2018-05-23 12:00'],
            ['2018-05-23 17:23', Interval::hours(18), '2018-05-23 12:00'],
            ['2018-05-23 17:23', Interval::day(), '2018-05-23 00:00'],
        ];
    }

    /**
     * Test rounding a time to various precision intervals.
     *
     * @dataProvider roundProvider
     *
     * @param string   $time
     * @param Interval $precision
     * @param string   $expectedRoundedTime
     */
    public function testRound(
        string $time,
        Interval $precision = null,
        string $expectedRoundedTime = ''
    ) {
        // Given we have a business time instance for a specific time;
        $businessTime = new BusinessTime($time);

        // When we round it to a precision interval.
        $rounded = $businessTime->round($precision);

        // Then we should get the expected rounded time.
        self::assertSame(
            $expectedRoundedTime,
            $rounded->format('Y-m-d H:i'),
            sprintf(
                '%s rounded to %s should be %s; got %s.',
                $time,
                ($precision ?: $businessTime->precision())->forHumans(),
                $expectedRoundedTime,
                $rounded->format('Y-m-d H:i')
            )
        );
    }

    /**
     * Provides times and the expected new time after rounding to various
     * precision intervals.
     *
     * @return array[]
     */
    public function roundProvider(): array
    {
        return [
            ['2018-05-23 17:23', null, '2018-05-23 17:00'], // default precision
            ['2018-05-23 17:23', Interval::minute(), '2018-05-23 17:23'],
            ['2018-05-23 17:23', Interval::minutes(10), '2018-05-23 17:20'],
            ['2018-05-23 17:23', Interval::minutes(15), '2018-05-23 17:30'],
            ['2018-05-23 17:23', Interval::minutes(30), '2018-05-23 17:30'],
            ['2018-05-23 17:23', Interval::hour(), '2018-05-23 17:00'],
            ['2018-05-23 17:23', Interval::hours(2), '2018-05-23 18:00'],
            ['2018-05-23 17:23', Interval::hours(3), '2018-05-23 18:00'],
            ['2018-05-23 17:23', Interval::hours(6), '2018-05-23 18:00'],
            ['2018-05-23 17:23', Interval::hours(8), '2018-05-23 16:00'],
            ['2018-05-23 17:23', Interval::hours(12), '2018-05-23 12:00'],
            ['2018-05-23 17:23', Interval::hours(18), '2018-05-23 12:00'],
            ['2018-05-23 17:23', Interval::day(), '2018-05-24 00:00'],
        ];
    }

    /**
     * Test ceiling a time to various precision intervals.
     *
     * @dataProvider ceilProvider
     *
     * @param string   $time
     * @param Interval $precision
     * @param string   $expectedCeilTime
     */
    public function testCeil(
        string $time,
        Interval $precision = null,
        string $expectedCeilTime = ''
    ) {
        // Given we have a business time instance for a specific time;
        $businessTime = new BusinessTime($time);

        // When we ceil it to a precision interval.
        $ceil = $businessTime->ceil($precision);

        // Then we should get the expected ceil-ed time.
        self::assertSame(
            $expectedCeilTime,
            $ceil->format('Y-m-d H:i'),
            sprintf(
                'Ceil of %s to %s should be %s; got %s.',
                $time,
                ($precision ?: $businessTime->precision())->forHumans(),
                $expectedCeilTime,
                $ceil->format('Y-m-d H:i')
            )
        );
    }

    /**
     * Provides times and the expected new time after ceiling to various
     * precision intervals.
     *
     * @return array[]
     */
    public function ceilProvider(): array
    {
        return [
            ['2018-05-23 17:23', null, '2018-05-23 18:00'], // default precision
            ['2018-05-23 17:23', Interval::minute(), '2018-05-23 17:23'],
            ['2018-05-23 17:23', Interval::minutes(10), '2018-05-23 17:30'],
            ['2018-05-23 17:23', Interval::minutes(15), '2018-05-23 17:30'],
            ['2018-05-23 17:23', Interval::minutes(30), '2018-05-23 17:30'],
            ['2018-05-23 17:23', Interval::hour(), '2018-05-23 18:00'],
            ['2018-05-23 17:23', Interval::hours(2), '2018-05-23 18:00'],
            ['2018-05-23 17:23', Interval::hours(3), '2018-05-23 18:00'],
            ['2018-05-23 17:23', Interval::hours(6), '2018-05-23 18:00'],
            ['2018-05-23 17:23', Interval::hours(8), '2018-05-24 00:00'],
            ['2018-05-23 17:23', Interval::hours(12), '2018-05-24 00:00'],
            ['2018-05-23 17:23', Interval::hours(18), '2018-05-24 06:00'],
            ['2018-05-23 17:23', Interval::day(), '2018-05-24 00:00'],
        ];
    }
}

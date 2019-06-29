<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use BusinessTime\Interval;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Test that business time calculations work correctly in different timezones.
 */
class TimeZoneTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        // Reset the default timezone.
        date_default_timezone_set('UTC');
        Carbon::setTestNow(Carbon::now()->setTimezone('UTC'));
    }

    /**
     * @dataProvider timezoneProvider
     *
     * @param string $timezone
     */
    public function testFloorToHourInTimezone(string $timezone)
    {
        // Given we are in a particular timezone.
        date_default_timezone_set($timezone);
        Carbon::setTestNow(Carbon::now()->setTimezone($timezone));

        // And we have a business time instance for a specific time;
        $businessTime = new BusinessTime(
            "2018-05-23 17:48 {$timezone}"
        );
        $businessTime = $businessTime->setTimezone($timezone);

        // When we floor it to the nearest hour.
        $floored = $businessTime->floorToPrecision(Interval::hour());

        // Then we should get the expected floored time.
        self::assertSame(
            "2018-05-23 17:00 {$timezone}",
            $floored->format('Y-m-d H:i e'),
            <<<MSG
2018-05-23 17:48 {$timezone}
floored to the hour should be
2018-05-23 17:00 {$timezone};
got
{$floored->format('Y-m-d H:i e')}.
MSG
        );
    }

    /**
     * @dataProvider timezoneProvider
     *
     * @param string $timezone
     */
    public function testRoundDownToHourInTimezone(string $timezone)
    {
        // Given we are in a particular timezone.
        date_default_timezone_set($timezone);
        Carbon::setTestNow(Carbon::now()->setTimezone($timezone));

        // And we have a business time instance for a specific time;
        $businessTime = new BusinessTime(
            "2018-05-23 17:23 {$timezone}"
        );
        $businessTime = $businessTime->setTimezone($timezone);

        // When we round it to the nearest hour.
        $floored = $businessTime->roundToPrecision(Interval::hour());

        // Then we should get the expected rounded time.
        self::assertSame(
            "2018-05-23 17:00 {$timezone}",
            $floored->format('Y-m-d H:i e'),
            <<<MSG
2018-05-23 17:23 {$timezone}
rounded to the hour should be
2018-05-23 17:00 {$timezone};
got
{$floored->format('Y-m-d H:i e')}.
MSG
        );
    }

    /**
     * @dataProvider timezoneProvider
     *
     * @param string $timezone
     */
    public function testRoundUpToHourInTimezone(string $timezone)
    {
        // Given we are in a particular timezone.
        date_default_timezone_set($timezone);
        Carbon::setTestNow(Carbon::now()->setTimezone($timezone));

        // And we have a business time instance for a specific time;
        $businessTime = new BusinessTime(
            "2018-05-23 17:32 {$timezone}"
        );
        $businessTime = $businessTime->setTimezone($timezone);

        // When we round it to the nearest hour.
        $floored = $businessTime->roundToPrecision(Interval::hour());

        // Then we should get the expected rounded time.
        self::assertSame(
            "2018-05-23 18:00 {$timezone}",
            $floored->format('Y-m-d H:i e'),
            <<<MSG
2018-05-23 17:32 {$timezone}
floored to the hour should be
2018-05-23 18:00 {$timezone};
got
{$floored->format('Y-m-d H:i e')}.
MSG
        );
    }

    /**
     * @dataProvider timezoneProvider
     *
     * @param string $timezone
     */
    public function testCeilDownToHourInTimezone(string $timezone)
    {
        // Given we are in a particular timezone.
        date_default_timezone_set($timezone);
        Carbon::setTestNow(Carbon::now()->setTimezone($timezone));

        // And we have a business time instance for a specific time;
        $businessTime = new BusinessTime(
            "2018-05-23 17:23 {$timezone}"
        );
        $businessTime = $businessTime->setTimezone($timezone);

        // When we ceil it to the nearest hour.
        $floored = $businessTime->ceilToPrecision(Interval::hour());

        // Then we should get the expected rounded time.
        self::assertSame(
            "2018-05-23 18:00 {$timezone}",
            $floored->format('Y-m-d H:i e'),
            <<<MSG
2018-05-23 17:23 {$timezone}
ceiled to the hour should be
2018-05-23 18:00 {$timezone};
got
{$floored->format('Y-m-d H:i e')}.
MSG
        );
    }

    /**
     * @return array[]
     */
    public function timezoneProvider(): array
    {
        return [
            ['Africa/Bangui'],
            ['Africa/Kinshasa'],
            ['Africa/Windhoek'],
            ['America/Cayenne'],
            ['America/Guatemala'],
            ['America/New_York'],
            ['America/Toronto'],
            ['Antarctica/Casey'],
            ['Antarctica/Troll'],
            ['Antarctica/Vostok'],
            ['Arctic/Longyearbyen'],
            ['Asia/Bishkek'],
            ['Asia/Kathmandu'],
            ['Asia/Rangoon'],
            ['Asia/Seoul'],
            ['Asia/Shanghai'],
            ['Asia/Tehran'],
            ['Asia/Tokyo'],
            ['Atlantic/Reykjavik'],
            ['Australia/Adelaide'],
            ['Australia/Sydney'],
            ['Europe/Amsterdam'],
            ['Europe/London'],
            ['Europe/Zurich'],
            ['Indian/Antananarivo'],
            ['Indian/Reunion'],
            ['Pacific/Auckland'],
            ['Pacific/Kwajalein'],
            ['Pacific/Majuro'],
            ['Pacific/Saipan'],
            ['Pacific/Tongatapu'],
            ['UTC'],
        ];
    }
}

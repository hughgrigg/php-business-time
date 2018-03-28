<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::startOfBusinessDay() and
 * BusinessTime::endOfBusinessDay() methods.
 */
class StartEndOfBusinessDayTest extends TestCase
{
    /**
     * Should be able to determine the start of the business day for a given
     * time with default behaviour.
     *
     * @dataProvider startOfBusinessDayProvider
     *
     * @param string $time
     * @param string $expectedStartOfBusinessDay
     */
    public function testStartOfBusinessDayDefault(
        string $time,
        string $expectedStartOfBusinessDay
    ): void {
        // Given we have a business time for a particular time;
        $businessTime = new BusinessTime($time);

        // When we get the start of the business day;
        $startOfBusinessDay = $businessTime->startOfBusinessDay();

        // Then it should be as expected.
        self::assertSame(
            $expectedStartOfBusinessDay,
            $startOfBusinessDay->format('l Y-m-d H:i'),
            sprintf(
                'Start of business day for %s should be %s; got %s',
                $time,
                $expectedStartOfBusinessDay,
                $startOfBusinessDay->format('l Y-m-d H:i')
            )
        );
    }

    /**
     * Provides times and the expected start of the business day for that
     * time with default behaviour, i.e. that business hours are 09:00 to
     * 17:00 Monday to Friday.
     *
     * @return array[]
     */
    public function startOfBusinessDayProvider(): array
    {
        return [
            ['Wednesday 2018-05-23 00:00', 'Wednesday 2018-05-23 09:00'],
            ['Wednesday 2018-05-23 08:00', 'Wednesday 2018-05-23 09:00'],
            ['Wednesday 2018-05-23 09:00', 'Wednesday 2018-05-23 09:00'],
            ['Wednesday 2018-05-23 10:00', 'Wednesday 2018-05-23 09:00'],
            ['Wednesday 2018-05-23 16:00', 'Wednesday 2018-05-23 09:00'],
            ['Wednesday 2018-05-23 17:00', 'Wednesday 2018-05-23 09:00'],
            ['Wednesday 2018-05-23 18:00', 'Wednesday 2018-05-23 09:00'],
            ['Wednesday 2018-05-23 23:00', 'Wednesday 2018-05-23 09:00'],
            ['Saturday 2018-05-26 18:00', 'Monday 2018-05-28 09:00'],
            ['Sunday 2018-05-26 19:00', 'Monday 2018-05-28 09:00'],
        ];
    }

    /**
     * Should be able to determine the end of the business day for a given
     * time with default behaviour, i.e. that business hours are 09:00 to
     * 17:00 Monday to Friday.
     *
     * @dataProvider endOfBusinessDayProvider
     *
     * @param string $time
     * @param string $expectedEndOfBusinessDay
     */
    public function testEndOfBusinessDayDefault(
        string $time,
        string $expectedEndOfBusinessDay
    ): void {
        // Given we have a business time for a particular time;
        $businessTime = new BusinessTime($time);

        // When we get the end of the business day;
        $endOfBusinessDay = $businessTime->endOfBusinessDay();

        // Then it should be as expected.
        self::assertSame(
            $expectedEndOfBusinessDay,
            $endOfBusinessDay->format('l Y-m-d H:i'),
            sprintf(
                'End of business day for %s should be %s; got %s',
                $time,
                $expectedEndOfBusinessDay,
                $endOfBusinessDay->format('l Y-m-d H:i')
            )
        );
    }

    /**
     * Provides times and the expected end of the business day for that
     * time with default behaviour, i.e. that business hours are 09:00 to
     * 17:00 Monday to Friday.
     *
     * @return array[]
     */
    public function endOfBusinessDayProvider(): array
    {
        return [
            ['Wednesday 2018-05-23 00:00', 'Wednesday 2018-05-23 17:00'],
            ['Wednesday 2018-05-23 08:00', 'Wednesday 2018-05-23 17:00'],
            ['Wednesday 2018-05-23 09:00', 'Wednesday 2018-05-23 17:00'],
            ['Wednesday 2018-05-23 10:00', 'Wednesday 2018-05-23 17:00'],
            ['Wednesday 2018-05-23 16:00', 'Wednesday 2018-05-23 17:00'],
            ['Wednesday 2018-05-23 17:00', 'Wednesday 2018-05-23 17:00'],
            ['Wednesday 2018-05-23 18:00', 'Wednesday 2018-05-23 17:00'],
            ['Wednesday 2018-05-23 23:00', 'Wednesday 2018-05-23 17:00'],
            ['Saturday 2018-05-26 03:00', 'Friday 2018-05-25 17:00'],
            ['Sunday 2018-05-26 02:00', 'Friday 2018-05-25 17:00'],
        ];
    }
}

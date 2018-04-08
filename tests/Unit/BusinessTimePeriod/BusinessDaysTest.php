<?php

namespace BusinessTime\Tests\Unit\BusinessTimePeriod;

use BusinessTime\BusinessTime;
use BusinessTime\BusinessTimePeriod;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTimePeriod::businessDays() and
 * BusinessTimePeriod::nonBusinessDays() methods.
 */
class BusinessDaysTest extends TestCase
{
    /**
     * @dataProvider businessDaysDefaultProvider
     *
     * @param string $startTime
     * @param string $endTime
     * @param array  $expectedBusinessDays
     */
    public function testBusinessDaysDefault(
        string $startTime,
        string $endTime,
        array $expectedBusinessDays
    ): void {
        // Given we have a business time period with a particular start and end;
        $timePeriod = BusinessTimePeriod::fromTo($startTime, $endTime);

        // When we get the business days inside it;
        $businessDays = array_map(
            function (BusinessTime $day): string {
                return $day->format('l');
            },
            $timePeriod->businessDays()
        );

        // Then they should be as expected.
        self::assertSame($expectedBusinessDays, $businessDays);
    }

    /**
     * Provides start and end times, and the expected business days between
     * them.
     *
     * @return array[]
     */
    public function businessDaysDefaultProvider(): array
    {
        return [
            // Start               End                  Days
            ['Monday 2018-05-21', 'Monday 2018-05-21', ['Monday']],
            ['Monday 2018-05-21', 'Tuesday 2018-05-22', ['Monday', 'Tuesday']],
            [
                'Wednesday 2018-05-23',
                'Friday 2018-05-25',
                ['Wednesday', 'Thursday', 'Friday'],
            ],
            [
                'Friday 2018-05-25',
                'Monday 2018-05-28',
                ['Friday', 'Monday'],
            ],
            ['Saturday 2018-05-26', 'Sunday 2018-05-27', []],
        ];
    }

    /**
     * @dataProvider nonBusinessDaysDefaultProvider
     *
     * @param string $startTime
     * @param string $endTime
     * @param array  $expectedBusinessDays
     */
    public function testNonBusinessDaysDefault(
        string $startTime,
        string $endTime,
        array $expectedBusinessDays
    ): void {
        // Given we have a business time period with a particular start and end;
        $timePeriod = BusinessTimePeriod::fromTo($startTime, $endTime);

        // When we get the non-business days inside it;
        $businessDays = array_map(
            function (BusinessTime $day): string {
                return $day->format('l');
            },
            $timePeriod->nonBusinessDays()
        );

        // Then they should be as expected.
        self::assertSame($expectedBusinessDays, $businessDays);
    }

    /**
     * Provides start and end times, and the expected non-business days between
     * them.
     *
     * @return array[]
     */
    public function nonBusinessDaysDefaultProvider(): array
    {
        return [
            // Start                  End                 Days
            ['Wednesday 2018-05-23', 'Friday 2018-05-25', []],
            ['Friday 2018-05-25', 'Saturday 2018-05-26', ['Saturday']],
            ['Friday 2018-05-25', 'Monday 2018-05-28', ['Saturday', 'Sunday']],
            ['Sunday 2018-05-27', 'Tuesday 2018-05-29', ['Sunday']],
        ];
    }

    /**
     * @dataProvider daysProvider
     *
     * @param string   $startTime
     * @param string   $endTime
     * @param string[] $expectedDays
     */
    public function testDays(
        string $startTime,
        string $endTime,
        array $expectedDays
    ): void {
        // Given we have a business time period with a particular start and end;
        $timePeriod = BusinessTimePeriod::fromTo($startTime, $endTime);

        // When we get the business days inside it;
        $days = array_map(
            function (BusinessTime $day): string {
                return $day->format('l');
            },
            $timePeriod->allDays()
        );

        // Then they should be as expected.
        self::assertSame(
            $expectedDays,
            $days,
            print_r($expectedDays, true) . "\nvs\n" . print_r(
                $days,
                true
            )
        );
    }

    /**
     * Provides start and end times, and the expected days between them.
     *
     * @return array[]
     */
    public function daysProvider(): array
    {
        return [
            // Start               End                  Days
            ['Monday 2018-05-21', 'Monday 2018-05-21', ['Monday']],
            ['Monday 2018-05-21', 'Tuesday 2018-05-22', ['Monday', 'Tuesday']],
            [
                'Wednesday 2018-05-23',
                'Friday 2018-05-25',
                ['Wednesday', 'Thursday', 'Friday'],
            ],
            [
                'Friday 2018-05-25',
                'Monday 2018-05-28',
                ['Friday', 'Saturday', 'Sunday', 'Monday'],
            ],
        ];
    }
}

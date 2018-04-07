<?php

namespace BusinessTime\Tests\Unit\BusinessTimePeriod;

use BusinessTime\BusinessTimePeriod;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTimePeriod sub-period methods.
 */
class SubPeriodsTest extends TestCase
{
    /**
     * @dataProvider businessPeriodsDefaultProvider
     *
     * @param string   $startTime
     * @param string   $endTime
     * @param string[] $expectedBusinessPeriodTimings Start/end time pairs.
     */
    public function testBusinessPeriodsDefault(
        string $startTime,
        string $endTime,
        array $expectedBusinessPeriodTimings
    ): void {
        // Given we have a business time period with a start and end time;
        $timePeriod = BusinessTimePeriod::fromStrings($startTime, $endTime);

        // When we get the business sub-periods;
        $subPeriods = $timePeriod->businessPeriods();

        // Then their timings should be as expected;
        $subPeriodTimings = array_map(
            function (BusinessTimePeriod $subPeriod): array {
                return [
                    $subPeriod->start()->format('l H:i'),
                    $subPeriod->end()->format('l H:i'),
                ];
            },
            $subPeriods
        );
        self::assertSame($expectedBusinessPeriodTimings, $subPeriodTimings);
    }

    /**
     * Provides start and end times with the expected start and end timings of
     * the business sub-periods between them with default behaviour.
     *
     * @return array[]
     */
    public function businessPeriodsDefaultProvider(): array
    {
        // Start
        // End
        // Sub-period timing pairs
        return [
            [
                'Monday 2018-05-21 03:00',
                'Monday 2018-05-21 19:00',
                [
                    ['Monday 09:00', 'Monday 17:00'],
                ],
            ],
            [
                'Monday 2018-05-21 09:00',
                'Tuesday 2018-05-22 17:00',
                [
                    ['Monday 09:00', 'Monday 17:00'],
                    ['Tuesday 09:00', 'Tuesday 17:00'],
                ],
            ],
            [
                'Monday 2018-05-21 05:00',
                'Wednesday 2018-05-23 23:00',
                [
                    ['Monday 09:00', 'Monday 17:00'],
                    ['Tuesday 09:00', 'Tuesday 17:00'],
                    ['Wednesday 09:00', 'Wednesday 17:00'],
                ],
            ],
            [
                'Friday 2018-05-25 13:00',
                'Tuesday 2018-05-29 11:00',
                [
                    ['Friday 13:00', 'Friday 17:00'],
                    ['Monday 09:00', 'Monday 17:00'],
                    ['Tuesday 09:00', 'Tuesday 11:00'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider nonBusinessPeriodsDefaultProvider
     *
     * @param string   $startTime
     * @param string   $endTime
     * @param string[] $expectedNonBusinessPeriodTimings Start/end time pairs.
     */
    public function testNonBusinessPeriodsDefault(
        string $startTime,
        string $endTime,
        array $expectedNonBusinessPeriodTimings
    ): void {
        // Given we have a business time period with a start and end time;
        $timePeriod = BusinessTimePeriod::fromStrings($startTime, $endTime);

        // When we get the non-business sub-periods;
        $subPeriods = $timePeriod->nonBusinessPeriods();

        // Then their timings should be as expected;
        $subPeriodTimings = array_map(
            function (BusinessTimePeriod $subPeriod): array {
                return [
                    $subPeriod->start()->format('l H:i'),
                    $subPeriod->end()->format('l H:i'),
                ];
            },
            $subPeriods
        );
        self::assertSame($expectedNonBusinessPeriodTimings, $subPeriodTimings);
    }

    /**
     * Provides start and end times with the expected start and end timings of
     * the non-business sub-periods between them with default behaviour.
     *
     * @return array[]
     */
    public function nonBusinessPeriodsDefaultProvider(): array
    {
        // Start
        // End
        // Sub-period timing pairs
        return [
            [
                'Monday 2018-05-21 03:00',
                'Monday 2018-05-21 19:00',
                [
                    ['Monday 03:00', 'Monday 09:00'],
                    ['Monday 17:00', 'Monday 19:00'],
                ],
            ],
            [
                'Monday 2018-05-21 09:00',
                'Tuesday 2018-05-22 17:00',
                [
                    ['Monday 17:00', 'Tuesday 09:00'],
                ],
            ],
            [
                'Monday 2018-05-21 05:00',
                'Wednesday 2018-05-23 23:00',
                [
                    ['Monday 05:00', 'Monday 09:00'],
                    ['Monday 17:00', 'Tuesday 09:00'],
                    ['Tuesday 17:00', 'Wednesday 09:00'],
                    ['Wednesday 17:00', 'Wednesday 23:00'],
                ],
            ],
            [
                'Friday 2018-05-25 13:00',
                'Tuesday 2018-05-29 11:00',
                [
                    ['Friday 17:00', 'Monday 09:00'],
                    ['Monday 17:00', 'Tuesday 09:00'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider subPeriodsDefaultProvider
     *
     * @param string   $startTime
     * @param string   $endTime
     * @param string[] $expectedSubPeriodTimings Start/end time pairs.
     */
    public function testSubPeriodsDefault(
        string $startTime,
        string $endTime,
        array $expectedSubPeriodTimings
    ): void {
        // Given we have a business time period with a start and end time;
        $timePeriod = BusinessTimePeriod::fromStrings($startTime, $endTime);

        // When we get the sub-periods;
        $subPeriods = $timePeriod->subPeriods();

        // Then their timings should be as expected;
        $subPeriodTimings = array_map(
            function (BusinessTimePeriod $subPeriod): array {
                return [
                    $subPeriod->start()->format('l H:i'),
                    $subPeriod->end()->format('l H:i'),
                ];
            },
            $subPeriods
        );
        self::assertSame($expectedSubPeriodTimings, $subPeriodTimings);
    }

    /**
     * Provides start and end times with the expected start and end timings of
     * the sub-periods between them with default behaviour.
     *
     * @return array[]
     */
    public function subPeriodsDefaultProvider(): array
    {
        return [
            // Start
            // End
            // Sub-period timing pairs
            [
                'Monday 2018-05-21 03:00',
                'Monday 2018-05-21 19:00',
                [
                    ['Monday 03:00', 'Monday 09:00'],
                    ['Monday 09:00', 'Monday 17:00'],
                    ['Monday 17:00', 'Monday 19:00'],
                ],
            ],
            [
                'Monday 2018-05-21 09:00',
                'Tuesday 2018-05-22 17:00',
                [
                    ['Monday 09:00', 'Monday 17:00'],
                    ['Monday 17:00', 'Tuesday 09:00'],
                    ['Tuesday 09:00', 'Tuesday 17:00'],
                ],
            ],
            [
                'Monday 2018-05-21 05:00',
                'Wednesday 2018-05-23 23:00',
                [
                    ['Monday 05:00', 'Monday 09:00'],
                    ['Monday 09:00', 'Monday 17:00'],
                    ['Monday 17:00', 'Tuesday 09:00'],
                    ['Tuesday 09:00', 'Tuesday 17:00'],
                    ['Tuesday 17:00', 'Wednesday 09:00'],
                    ['Wednesday 09:00', 'Wednesday 17:00'],
                    ['Wednesday 17:00', 'Wednesday 23:00'],
                ],
            ],
            [
                'Friday 2018-05-25 13:00',
                'Tuesday 2018-05-29 11:00',
                [
                    ['Friday 13:00', 'Friday 17:00'],
                    ['Friday 17:00', 'Monday 09:00'],
                    ['Monday 09:00', 'Monday 17:00'],
                    ['Monday 17:00', 'Tuesday 09:00'],
                    ['Tuesday 09:00', 'Tuesday 11:00'],
                ],
            ],
        ];
    }
}

<?php

namespace BusinessTime\Tests\Unit\BusinessTimePeriod;

use BusinessTime\BusinessTimePeriod;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTimePeriod::businessName method.
 *
 * @group BusinessTimePeriod
 */
class BusinessNameTest extends TestCase
{
    /**
     * @dataProvider businessNameDefaultProvider
     *
     * @param string $startTime
     * @param string $endTime
     * @param string $expectedBusinessName
     */
    public function testBusinessNameDefault(
        string $startTime,
        string $endTime,
        string $expectedBusinessName
    ): void {
        // Given we have a business time period with a particular start and end;
        $timePeriod = BusinessTimePeriod::fromStrings($startTime, $endTime);

        // When we get its business-relevant name;
        $businessName = $timePeriod->businessName();

        // Then it should be as expected.
        self::assertSame($expectedBusinessName, $businessName);
    }

    /**
     * Provides start and end times with the expected business-relevant name of
     * the intervening period with default behaviour.
     *
     * @return array[]
     */
    public function businessNameDefaultProvider(): array
    {
        return [
            // Start End Name
            [
                'Monday 2018-05-21 09:00',
                'Monday 2018-05-21 17:00',
                'business hours',
            ],
            [
                'Monday 2018-05-21 17:00',
                'Monday 2018-05-21 20:00',
                'outside business hours',
            ],
            [
                'Monday 2018-05-21 17:00',
                'Tuesday 2018-05-22 09:00',
                'outside business hours',
            ],
            [
                'Friday 2018-05-25 17:00',
                'Monday 2018-05-28 09:00',
                'the weekend',
            ],
            // Mixed periods default to the most commonly occurring name.
            [
                'Monday 2018-05-21 03:00',
                'Monday 2018-05-21 19:00',
                'outside business hours',
            ],
            [
                'Monday 2018-05-21 09:00',
                'Tuesday 2018-05-22 17:00',
                'business hours',
            ],
            [
                'Monday 2018-05-21 05:00',
                'Wednesday 2018-05-23 23:00',
                'outside business hours',
            ],
            [
                'Friday 2018-05-25 13:00',
                'Tuesday 2018-05-29 11:00',
                'the weekend',
            ],
        ];
    }
}

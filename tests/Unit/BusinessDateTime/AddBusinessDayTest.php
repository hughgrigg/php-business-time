<?php

namespace BusinessTime\Tests\Unit\BusinessDateTime;

use BusinessTime\BusinessTime;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::addBusinessDay() method.
 */
class AddBusinessDayTest extends TestCase
{
    /**
     * Test the addBusinessDay method with default behaviour.
     *
     * @dataProvider addBusinessDayProvider
     *
     * @param string $time
     * @param string $expectedNewTime
     */
    public function testAddBusinessDayDefault(
        string $time,
        string $expectedNewTime
    ): void {
        // Given we have a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // When we add a business day to it;
        $nextBusinessDay = $businessTime->addBusinessDay();

        // Then we should get the expected new time.
        self::assertSame(
            $expectedNewTime,
            $nextBusinessDay->format('l jS F Y H:i')
        );
    }

    /**
     * Provides times with the expected new time after adding one business day
     * with default behaviour, i.e. that working time is Monday to Friday
     * 09:00 to 17:00.
     *
     * @return array[]
     */
    public function addBusinessDayProvider(): array
    {
        return [
            ['Monday 14th May 2018 00:00', 'Monday 14th May 2018 17:00'],
            ['Monday 14th May 2018 08:00', 'Monday 14th May 2018 17:00'],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 17:00'],
            ['Monday 14th May 2018 10:00', 'Tuesday 15th May 2018 10:00'],
            ['Monday 14th May 2018 17:00', 'Tuesday 15th May 2018 17:00'],
            ['Tuesday 15th May 2018 11:00', 'Wednesday 16th May 2018 11:00'],
            ['Wednesday 16th May 2018 12:00', 'Thursday 17th May 2018 12:00'],
            ['Thursday 17th May 2018 13:00', 'Friday 18th May 2018 13:00'],
            ['Friday 18th May 2018 14:00', 'Monday 21st May 2018 14:00'],
            ['Saturday 19th May 2018 15:00', 'Monday 21st May 2018 17:00'],
            ['Sunday 20th May 2018 16:00', 'Monday 21st May 2018 17:00'],
        ];
    }
}

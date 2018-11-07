<?php

namespace BusinessTime\Tests\Functional\Remote\WebCalFi;

use BusinessTime\Remote\WebCalFi\WebCalFiFactory;
use DateTime;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

/**
 * Functionally test the WebCalFiFactory class with real network access.
 */
class WebCalFiFactoryTest extends TestCase
{
    /**
     * Skip tests if it it's not possible to connect to WebCal.Fi.
     */
    public function setUp()
    {
        parent::setUp();

        try {
            (new Client())->get('https://www.webcal.fi');
        } catch (\Throwable $e) {
            self::markTestSkipped(
                sprintf(
                    'Unable to connect to https://www.webcal.fi : %s',
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @slowThreshold 1500
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUkHolidaysContainsChristmas()
    {
        // Given we have a WebCalFiFactory;
        $factory = new WebCalFiFactory(new Client());

        // And it's using the UK public holidays calendar;
        $factory->setCalendarUrl(
            'https://www.webcal.fi/cal.php?id=83&format=json&start_year=2018&end_year=2018'
        );

        // When we get the holiday dates;
        $holidaysConstraint = $factory->makeConstraint();

        // Then Christmas Day should be a holiday.
        $christmasDay = new DateTime('25th December 2018');
        self::assertFalse(
            $holidaysConstraint->isBusinessTime($christmasDay),
            'Who cancelled Christmas?'
        );
        self::assertContains(
            'Christmas',
            $holidaysConstraint->narrate($christmasDay)
        );
    }

    /**
     * @slowThreshold 1500
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUsHolidaysHolidaysContainsIndependenceDay()
    {
        // Given we have a WebCalFiFactory;
        $factory = new WebCalFiFactory(new Client());

        // And it's using the US federal holidays calendar;
        $factory->setCalendarUrl(
            'https://www.webcal.fi/cal.php?id=52&format=json&start_year=2018&end_year=2018'
        );

        // When we get the holiday dates;
        $holidaysConstraint = $factory->makeConstraint();

        // Then Will Smith should be shooting aliens or something.
        $independenceDay = new DateTime('4th July 2018');
        self::assertFalse(
            $holidaysConstraint->isBusinessTime($independenceDay),
            'Independence Day should not be business time.'
        );
        self::assertContains(
            'Independence',
            $holidaysConstraint->narrate($independenceDay)
        );
    }
}

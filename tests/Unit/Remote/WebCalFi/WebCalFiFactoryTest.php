<?php

namespace BusinessTime\Tests\Unit\Remote\WebCalFi;

use BusinessTime\Remote\WebCalFi\WebCalFiDate;
use BusinessTime\Remote\WebCalFi\WebCalFiFactory;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Test the WebCalFiFactory with the network mocked out.
 */
class WebCalFiFactoryTest extends TestCase
{
    /** @var ClientInterface|MockObject */
    private $client;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();

        $this->client = $this->createMock(ClientInterface::class);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetDatesMocked(): void
    {
        // Given we have a WebCalFi constraint factory;
        $factory = new WebCalFiFactory($this->client);
        $factory->setUrl('https://www.webcal.fi/example');

        // And the WebCal.fi service has some dates;
        $this->client->expects($this->any())
            ->method('request')
            ->willReturn(
                new Response(
                    200,
                    [],
                    <<<JSON
[
    {
        "date": "2018-01-01",
        "name": "New Year's Day",
        "url": "https://webcal.fi/example",
        "description": "1st January is New Year's Day"
    },
    {
        "date": "2018-05-07",
        "name": "Early May bank holiday",
        "url": "https://webcal.fi/example",
        "description": "7th May 2018 is the Early May bank holiday"
    }
]
JSON
                )
            );

        // When we ask the factory for the dates;
        /** @var WebCalFiDate[] $dates */
        $dates = $factory->getDates();

        // Then we should get the expected results.
        $newYearsDay = $dates[0];
        self::assertSame(
            'Monday 1st January 2018',
            $newYearsDay->date->format('l jS F Y')
        );
        self::assertSame("New Year's Day", $newYearsDay->name);

        $mayHoliday = $dates[1];
        self::assertSame(
            'Monday 7th May 2018',
            $mayHoliday->date->format('l jS F Y')
        );
        self::assertSame('Early May bank holiday', $mayHoliday->name);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testHttpStatusError(): void
    {
        // Given we have a WebCalFi constraint factory;
        $factory = new WebCalFiFactory($this->client);

        // And the WebCal.fi service is not in a good mood;
        $this->client->expects($this->any())
            ->method('request')
            ->willReturn(new Response(500));

        // Then we should get a run-time exception;
        $this->expectException(RuntimeException::class);

        // When we ask the factory for the dates.
        $factory->getDates();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testBadJsonError(): void
    {
        // Given we have a WebCalFi constraint factory;
        $factory = new WebCalFiFactory($this->client);

        // And the WebCal.fi service is returning bad JSON today;
        $this->client->expects($this->any())
            ->method('request')
            ->willReturn(new Response(200), [], '[{');

        // Then we should get a run-time exception;
        $this->expectException(RuntimeException::class);

        // When we ask the factory for the dates.
        $factory->getDates();
    }
}

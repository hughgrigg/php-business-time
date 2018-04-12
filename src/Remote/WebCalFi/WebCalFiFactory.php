<?php

namespace BusinessTime\Remote\WebCalFi;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

/**
 * Makes a business time constraint from dates retrieved from the WebCal.fi
 * service.
 *
 * @see https://www.webcal.fi/
 * @see WebCalFiFactoryTest
 */
class WebCalFiFactory
{
    /** @var ClientInterface */
    private $client;

    /** @var string */
    private $url;

    /** @var WebCalFiDate[] Cache of retrieved dates */
    private $dates;

    /**
     * @param ClientInterface $client
     * @param string          $url
     *
     * e.g. https://www.webcal.fi/cal.php?id=83&format=json
     *
     * @see https://www.webcal.fi/en-GB/calendars.php
     */
    public function __construct(ClientInterface $client, string $url = '')
    {
        $this->client = $client;
        $this->url = $url;
    }

    /**
     * @return WebCalFiConstraint
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makeConstraint(): WebCalFiConstraint
    {
        return new WebCalFiConstraint(...$this->getDates());
    }

    /**
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return WebCalFiDate[]
     */
    public function getDates(): array
    {
        if ($this->dates === null) {
            $response = $this->getWebCalFiResponse();
            $decoded = $this->decodeWebCalFiResponse($response);

            $this->dates = array_map(
                function (stdClass $date): WebCalFiDate {
                    return new WebCalFiDate(
                        $date->date,
                        $date->name ?? '',
                        $date->url ?? '',
                        $date->description ?? ''
                    );
                },
                $decoded
            );
        }

        return $this->dates;
    }

    /**
     * Set the WebCalFi calendar URL.
     *
     * @param string $url
     *
     * @return WebCalFiFactory
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return ResponseInterface
     */
    private function getWebCalFiResponse(): ResponseInterface
    {
        $response = $this->client->request('GET', $this->url);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(
                sprintf(
                    'Got HTTP status %d from %s: %s',
                    $response->getStatusCode(),
                    $this->url,
                    (string) $response->getBody()
                )
            );
        }

        return $response;
    }

    /**
     * @param $response
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    private function decodeWebCalFiResponse(ResponseInterface $response): array
    {
        $decoded = \json_decode((string) $response->getBody());
        if (!$decoded) {
            throw new \RuntimeException(
                sprintf(
                    'Failed to decode WebCal.fi dates from %s: %s : %s',
                    $this->url,
                    \json_last_error_msg(),
                    (string) $response->getBody()
                )
            );
        }

        return $decoded;
    }
}

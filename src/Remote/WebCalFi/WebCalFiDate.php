<?php

namespace BusinessTime\Remote\WebCalFi;

use Carbon\Carbon;

/**
 * Value object for a date retrieved from WebCal.fi.
 *
 * E.g. from https://www.webcal.fi/cal.php?id=83&format=json
 */
class WebCalFiDate
{
    /** @var Carbon */
    public $date;

    /** @var string */
    public $name;

    /** @var string */
    public $url;

    /** @var string */
    public $description;

    /**
     * WebCalFiDate constructor.
     *
     * @param string $date
     * @param string $name
     * @param string $url
     * @param string $description
     */
    public function __construct(
        string $date,
        string $name = '',
        string $url = '',
        string $description = ''
    ) {
        $this->date = new Carbon($date);
        $this->name = $name;
        $this->url = $url;
        $this->description = $description;
    }
}

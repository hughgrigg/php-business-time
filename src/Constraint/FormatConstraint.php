<?php

namespace BusinessTime\Constraint;

use BusinessTime\Constraint\Composite\Combinations;
use BusinessTime\Constraint\Narration\BusinessTimeNarrator;
use DateTimeInterface;

/**
 * Constraint that matches business times using a date time format and
 * corresponding matching string.
 *
 * e.g. new FormatConstraint('l', 'Monday') matches any time on a Monday as
 * business time.
 */
class FormatConstraint implements BusinessTimeConstraint, BusinessTimeNarrator
{
    use Combinations;

    /** @var string */
    private $format;

    /** @var string[] */
    private $matches;

    /**
     * @param string $format
     * @param string ...$matches
     */
    public function __construct(string $format, string ...$matches)
    {
        $this->format = $format;
        $this->matches = $matches;
    }

    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTimeInterface $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTimeInterface $time): bool
    {
        return \in_array($time->format($this->format), $this->matches, true);
    }

    /**
     * Get a business-relevant description for the given time.
     *
     * @param DateTimeInterface $time
     *
     * @return string
     */
    public function narrate(DateTimeInterface $time): string
    {
        return $time->format($this->format);
    }
}

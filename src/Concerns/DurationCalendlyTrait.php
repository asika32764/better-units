<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Concerns;

trait DurationCalendlyTrait
{
    /**
     * Common year seconds is a fixed length of time used in many civil calendars, which has a common year of 365 days.
     * It is often used in financial calculations and other applications where a fixed length of time is required.
     */
    public const string YEAR_SECONDS_COMMON = '31536000';

    /**
     * Leap year seconds is a fixed length of time used in many civil calendars, which has a leap year of 366 days.
     * It is often used in financial calculations and other applications where a fixed length of time is required.
     */
    public const string YEAR_SECONDS_LEAP = '31622400';

    /**
     * Gregorian year seconds is a fixed length of time used in the Gregorian calendar, which has a common year of
     * 365 days and a leap year of 366 days.
     */
    public const string YEAR_SECONDS_GREGORIAN = '31556952';

    /**
     * Julian year seconds is a fixed length of time used in astronomy and other scientific calculations,
     * which has a common year of 365.25 days.
     */
    public const string YEAR_SECONDS_JULIAN = '31557600';

    /**
     * Tropical year seconds is the time it takes for the Earth to complete one orbit around the Sun, measured from
     * vernal equinox to the next.
     */
    public const string YEAR_SECONDS_TROPICAL = '31556925.97';

    /**
     * Sidereal year seconds is the time it takes for the Earth to complete one orbit around the Sun relative to
     * the fixed stars, which is slightly longer than the tropical year.
     */
    public const string YEAR_SECONDS_SIDEREAL = '31558149.76';

    /**
     * Anomalistic year seconds is the time it takes for the Earth to return to the same point in its elliptical
     * orbit around the Sun, which is slightly longer than the tropical year due to the elliptical shape
     * of Earth's orbit.
     */
    public const string YEAR_SECONDS_ANOMALISTIC = '31558432.55';

    /**
     * Draconic year seconds is the time it takes for the Earth to return to the same position relative to the
     * Moon's ascending node, which is the point where the Moon's orbit crosses the ecliptic plane.
     */
    public const string YEAR_SECONDS_DRACONIC = '29947971';

    /**
     * Gaussian year seconds is a time unit used in celestial mechanics, based on the average orbital period of
     * planets in the solar system, which is approximately 365.2568983 days.
     */
    public const string YEAR_SECONDS_GAUSSIAN = '31558196.01';

    /**
     * Common month seconds is a fixed length of time used in many civil calendars, which has a common month of
     * 30.44 days.
     */
    public const string MONTH_SECONDS_COMMON = '2629440';

    /**
     * Gregorian month seconds is a fixed length of time used in the Gregorian calendar, which has a common month
     * of 30.436875 days.
     */
    public const string MONTH_SECONDS_GREGORIAN = '2629746';

    /**
     * Julian month seconds is a fixed length of time used in astronomy and other scientific calculations,
     * which has a common month of 30.4375 days.
     */
    public const string MONTH_SECONDS_JULIAN = '2629800';

    /**
     * Tropical month seconds is the time it takes for the Moon to complete one orbit around the Earth relative
     * to the vernal equinox, which is approximately 27.32158days.
     */
    public const string MONTH_SECONDS_TROPICAL = '2360584.51';

    /**
     * Sidereal month seconds is the time it takes for the Moon to complete one orbit around the Earth relative
     * to the fixed stars, which is approximately 27.321661 days.
     */
    public const string MONTH_SECONDS_SIDEREAL = '2360591.5';

    /**
     * Anomalistic month seconds is the time it takes for the Moon to return to the same point in its elliptical
     * orbit around the Earth, which is approximately 27.55455 days.
     */
    public const string MONTH_SECONDS_ANOMALISTIC = '2380713.12';

    /**
     * Draconic month seconds is the time it takes for the Moon to return to the same position relative to the
     * Earth's ascending node, which is approximately 27.21222 days.
     */
    public const string MONTH_SECONDS_DRACONIC = '2351135.81';

    public const string MONTH_SECONDS_28DAYS = '2419200';

    public const string MONTH_SECONDS_29DAYS = '2505600';

    public const string MONTH_SECONDS_30DAYS = '2592000';

    public const string MONTH_SECONDS_31DAYS = '2678400';

    public function withCommonCalendar(): static
    {
        return $this
            ->withYearSeconds(static::YEAR_SECONDS_COMMON)
            ->withMonthSeconds(static::MONTH_SECONDS_COMMON);
    }

    public function withGregorianCalendar(): static
    {
        return $this
            ->withYearSeconds(static::YEAR_SECONDS_GREGORIAN)
            ->withMonthSeconds(static::MONTH_SECONDS_GREGORIAN);
    }

    public function withJulianCalendar(): static
    {
        return $this
            ->withYearSeconds(static::YEAR_SECONDS_JULIAN)
            ->withMonthSeconds(static::MONTH_SECONDS_JULIAN);
    }

    public function withAnomalisticCalendar(): static
    {
        return $this
            ->withYearSeconds(static::YEAR_SECONDS_ANOMALISTIC)
            ->withMonthSeconds(static::MONTH_SECONDS_ANOMALISTIC);
    }

    public function withTropicalCalendar(): static
    {
        return $this
            ->withYearSeconds(static::YEAR_SECONDS_TROPICAL)
            ->withMonthSeconds(static::MONTH_SECONDS_TROPICAL);
    }

    public function withSiderealCalendar(): static
    {
        return $this
            ->withYearSeconds(static::YEAR_SECONDS_SIDEREAL)
            ->withMonthSeconds(static::MONTH_SECONDS_SIDEREAL);
    }

    public function withDraconicCalendar(): static
    {
        return $this
            ->withYearSeconds(static::YEAR_SECONDS_DRACONIC)
            ->withMonthSeconds(static::MONTH_SECONDS_DRACONIC);
    }
}

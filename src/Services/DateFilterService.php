<?php

namespace Nyumbani\Essentials\Services;

use Illuminate\Support\Carbon;

/**
 * Parse a period filter (a day, month, year or range) into the dates a report is scoped to. Input that
 * is not a valid period falls back to the default period instead of failing.
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class DateFilterService
{
    /**
     * Parse a period string such as "2026", "2026-03", "2026-03-14" or "2026-01-01 to 2026-03-31"
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function parse(?string $value, string $default = 'month'): Period
    {
        //  Trim the input
        $value = trim((string) $value);

        //  Try each format in turn, falling back to the default period
        return $this->fromRange($value)
            ?? $this->fromDate($value)
            ?? $this->fromMonth($value)
            ?? $this->fromYear($value)
            ?? $this->defaultPeriod($default);
    }

    /**
     * Get the current day, month or year, which is the month unless the default is "day" or "year"
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function defaultPeriod(string $default): Period
    {
        //  Get the current moment
        $now = now();

        //  Build the period for the default
        return match ($default) {
            'day' => $this->make($now->copy()->startOfDay(), $now->copy()->endOfDay(), $now->format('Y-m-d')),
            'year' => $this->make($now->copy()->startOfYear(), $now->copy()->endOfYear(), $now->format('Y'), hasMonth: false),
            default => $this->make($now->copy()->startOfMonth(), $now->copy()->endOfMonth(), $now->format('Y-m')),
        };
    }

    /**
     * Parse a date range (e.g. "2026-01-01 to 2026-03-31"), which must not end before it starts
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function fromRange(string $value): ?Period
    {
        //  Split the range into its two dates
        $parts = explode(' to ', $value, 2);

        //  Parse the first date
        $from = $this->date($parts[0]);

        //  Parse the second date
        $to = $this->date($parts[1] ?? '');

        //  Reject a range that is incomplete or ends before it starts
        if (! $from || ! $to || $to->lt($from)) {
            return null;
        }

        //  Build the period
        return $this->make($from->copy()->startOfDay(), $to->copy()->endOfDay(), $value, hasYear: false, hasMonth: false);
    }

    /**
     * Parse a full date (e.g. "2026-01-14")
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function fromDate(string $value): ?Period
    {
        //  Parse the date
        $date = $this->date($value);

        //  Reject anything that is not a real date
        return $date
            ? $this->make($date->startOfDay(), $date->endOfDay(), $value)
            : null;
    }

    /**
     * Parse a month (e.g. "2026-03"), which must be a real month
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function fromMonth(string $value): ?Period
    {
        //  Match the year and month, with the month between 1 and 12
        if (! preg_match('/^(\d{4})-(\d{1,2})$/', $value, $matches) || ! checkdate((int) $matches[2], 1, (int) $matches[1])) {
            return null;
        }

        //  Build the first day of the month
        $date = Carbon::create((int) $matches[1], (int) $matches[2], 1);

        //  Build the period
        return $this->make($date->copy()->startOfMonth(), $date->copy()->endOfMonth(), $value);
    }

    /**
     * Parse a year (e.g. "2026")
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function fromYear(string $value): ?Period
    {
        //  Match a four digit year
        if (! preg_match('/^\d{4}$/', $value)) {
            return null;
        }

        //  Build the first day of the year
        $date = Carbon::create((int) $value, 1, 1);

        //  Build the period
        return $this->make($date->copy()->startOfYear(), $date->copy()->endOfYear(), $value, hasMonth: false);
    }

    /**
     * Parse a real calendar date such as "2026-01-14", or null for anything else (including "2026-02-31")
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function date(string $value): ?Carbon
    {
        //  Match the year, month and day, and check they form a real date
        if (! preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', trim($value), $matches) || ! checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1])) {
            return null;
        }

        //  Build the date
        return Carbon::create((int) $matches[1], (int) $matches[2], (int) $matches[3]);
    }

    /**
     * Build a period from its first and last moments, giving it its own copy of the date and leaving the year and month null when it spans several
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function make(Carbon $from, Carbon $to, string $formatted, bool $hasYear = true, bool $hasMonth = true): Period
    {
        return new Period(
            date: $from->copy(),
            year: $hasYear ? $from->year : null,
            month: $hasMonth ? $from->month : null,
            from: $from,
            to: $to,
            formatted: $formatted,
        );
    }
}

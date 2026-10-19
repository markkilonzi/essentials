<?php

namespace Nyumbani\Essentials\Services;

use Illuminate\Support\Carbon;

/**
 * The dates a report is scoped to: a day, month, year or range
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
final readonly class Period
{
    /**
     * Create a period, where year and month are null when the period spans more than one of them
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __construct(
        public Carbon $date,
        public ?int $year,
        public ?int $month,
        public Carbon $from,
        public Carbon $to,
        public string $formatted,
    ) {}

    /**
     * Convert the period to an array for view rendering
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'year' => $this->year,
            'month' => $this->month,
            'from' => $this->from,
            'to' => $this->to,
            'formatted' => $this->formatted,
        ];
    }

    /**
     * Merge the period into the view data under the `period` key
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function merge(array $data): array
    {
        return array_merge($data, ['period' => fluent($this->toArray())]);
    }
}

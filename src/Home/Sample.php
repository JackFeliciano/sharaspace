<?php

declare(strict_types=1);

namespace Sharaspace\Home;

/**
 * The spaces and the calendars the demo shows. Every figure on the page comes out
 * of these objects, so the page cannot claim a lease the code does not hold.
 */
final class Sample
{
    /** @var list<Calendar> */
    private array $calendars = [];

    public function __construct()
    {
        $floor = new Space('ss-1', 'Rua das Flores 2nd floor', 'Porto', 'office', '24000', ['fitted kitchen', 'eighty desks', 'fibre'], true);
        $calendar = new Calendar($floor);
        $calendar->let('ls-1', 'Mara Lind', new Term(Term::WEEK, 2), '2026-12-07');
        $calendar->let('ls-2', 'Bright Field', new Term(Term::MONTH, 3), '2026-12-21');
        $this->calendars[] = $calendar;

        $room = new Space('ss-2', 'The back room', 'Lisbon', 'restaurant', '60000', ['forty covers', 'service bar', 'extraction'], true);
        $calendar2 = new Calendar($room);
        $calendar2->let('ls-3', 'Kestrel Works', new Term(Term::MONTH, 12), '2027-01-04');
        $this->calendars[] = $calendar2;

        $bay = new Space('ss-3', 'Loading bay 4', 'Coimbra', 'warehouse', '9000', ['roller door', 'three phase power'], false);
        $this->calendars[] = new Calendar($bay);
    }

    /** @return list<Calendar> */
    public function calendars(): array
    {
        return $this->calendars;
    }

    public function count(): int
    {
        return count($this->calendars);
    }

    public function taken(): int
    {
        $sum = 0;
        foreach ($this->calendars as $calendar) {
            $sum += $calendar->taken();
        }

        return $sum;
    }

    public function billing(): int
    {
        $sum = 0;
        foreach ($this->calendars as $calendar) {
            $sum += $calendar->billing();
        }

        return $sum;
    }
}

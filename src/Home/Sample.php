<?php

declare(strict_types=1);

namespace Sharaspace\Home;

/**
 * The rooms and the diary the demo shows. Every figure on the page comes out of
 * these objects, so the page cannot claim a booking the code does not hold.
 */
final class Sample
{
    /** @var list<Diary> */
    private array $diaries = [];

    public function __construct()
    {
        $loft = new Room('ss-1', 'The Loft', 'Porto', 40, '24000', ['natural light', 'projector', 'kitchen'], true);
        $diary = new Diary($loft);
        $diary->book('bk-1', 'Mara Lind', new Slot('2026-12-05', 9, 13))->confirm();
        $diary->book('bk-2', 'Bright Field', new Slot('2026-12-05', 18, 22));
        $this->diaries[] = $diary;

        $hall = new Room('ss-2', 'The Hall', 'Lisbon', 120, '60000', ['stage', 'sound desk', 'bar'], true);
        $diary2 = new Diary($hall);
        $diary2->book('bk-3', 'Kestrel Works', new Slot('2027-01-16', 20, 24))->confirm();
        $this->diaries[] = $diary2;

        $studio = new Room('ss-3', 'Garden Studio', 'Coimbra', 8, '9000', ['daylight', 'whiteboard'], false);
        $this->diaries[] = new Diary($studio);
    }

    /** @return list<Diary> */
    public function diaries(): array
    {
        return $this->diaries;
    }

    public function count(): int
    {
        return count($this->diaries);
    }

    public function taken(): int
    {
        $sum = 0;
        foreach ($this->diaries as $diary) {
            $sum += $diary->taken();
        }

        return $sum;
    }

    public function takings(): int
    {
        $sum = 0;
        foreach ($this->diaries as $diary) {
            $sum += $diary->takings();
        }

        return $sum;
    }
}

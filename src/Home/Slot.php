<?php

declare(strict_types=1);

namespace Sharaspace\Home;

/**
 * A slot is a stretch of one day in one room, written as a start hour and an end
 * hour on a 24 hour clock. Two slots clash when their hours overlap.
 */
final class Slot
{
    public function __construct(
        public readonly string $day,
        public readonly int $from,
        public readonly int $to,
    ) {
        if ($from < 0 || $to > 24) {
            throw new \RuntimeException('a slot sits between hour 0 and hour 24');
        }
        if ($from >= $to) {
            throw new \RuntimeException('a slot ends after it starts');
        }
    }

    public function hours(): int
    {
        return $this->to - $this->from;
    }

    public function clashesWith(Slot $other): bool
    {
        if ($this->day !== $other->day) {
            return false;
        }

        return $this->from < $other->to && $other->from < $this->to;
    }

    public function label(): string
    {
        return $this->day . ' ' . str_pad((string) $this->from, 2, '0', STR_PAD_LEFT)
            . ' to ' . str_pad((string) $this->to, 2, '0', STR_PAD_LEFT);
    }
}

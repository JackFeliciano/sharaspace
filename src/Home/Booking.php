<?php

declare(strict_types=1);

namespace Sharaspace\Home;

/**
 * A booking ties one booker to one slot in one room. It holds the day rate the
 * room carried when the booker took it, so a later change cannot rewrite a bill.
 */
final class Booking
{
    public function __construct(
        public readonly string $reference,
        public readonly Room $room,
        public readonly string $booker,
        public readonly Slot $slot,
        private string $state = 'held',
    ) {
        if (!$room->isOpen()) {
            throw new \RuntimeException('a closed room takes no booking');
        }
        if ($slot->hours() > 12) {
            throw new \RuntimeException('a booking runs no longer than twelve hours in a day');
        }
    }

    public function state(): string
    {
        return $this->state;
    }

    public function cents(): int
    {
        return $this->room->rateFor($this->slot->hours());
    }

    public function confirm(): void
    {
        if ($this->state !== 'held') {
            throw new \RuntimeException('only a held booking confirms');
        }
        $this->state = 'confirmed';
    }

    public function cancel(): void
    {
        if ($this->state === 'cancelled') {
            throw new \RuntimeException('a cancelled booking stays cancelled');
        }
        $this->state = 'cancelled';
    }
}

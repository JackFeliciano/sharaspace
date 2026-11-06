<?php

declare(strict_types=1);

namespace Sharaspace\Home;

/**
 * The diary of one room. It holds the bookings and answers the only question a
 * booker asks: does this slot stand free.
 */
final class Diary
{
    /** @var list<Booking> */
    private array $bookings = [];

    public function __construct(public readonly Room $room)
    {
    }

    public function book(string $reference, string $booker, Slot $slot): Booking
    {
        foreach ($this->bookings as $held) {
            if ($held->state() !== 'cancelled' && $held->slot->clashesWith($slot)) {
                throw new \RuntimeException('that slot is already taken in ' . $this->room->name);
            }
        }
        $booking = new Booking($reference, $this->room, $booker, $slot);
        $this->bookings[] = $booking;

        return $booking;
    }

    /** @return list<Booking> */
    public function bookings(): array
    {
        return $this->bookings;
    }

    public function free(Slot $slot): bool
    {
        foreach ($this->bookings as $held) {
            if ($held->state() !== 'cancelled' && $held->slot->clashesWith($slot)) {
                return false;
            }
        }

        return true;
    }

    public function taken(): int
    {
        return count(array_filter($this->bookings, static fn (Booking $b): bool => $b->state() !== 'cancelled'));
    }

    public function takings(): int
    {
        $sum = 0;
        foreach ($this->bookings as $booking) {
            if ($booking->state() === 'cancelled') {
                continue;
            }
            $sum += $booking->cents();
        }

        return $sum;
    }
}

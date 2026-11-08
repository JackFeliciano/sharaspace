<?php

declare(strict_types=1);

namespace Sharaspace\Home;

/**
 * The calendar of one space. It answers the question a tenant asks before a
 * viewing: is this space free from this day for this term.
 */
final class Calendar
{
    /** @var list<Lease> */
    private array $leases = [];

    public function __construct(public readonly Space $space)
    {
    }

    public function let(string $reference, string $tenant, Term $term, string $from): Lease
    {
        if ($this->free($term, $from) === false) {
            throw new \RuntimeException('that term crosses a lease already in ' . $this->space->name);
        }
        $lease = new Lease($reference, $this->space, $tenant, $term, $from, $this->space->rateFor($term));
        $this->leases[] = $lease;

        return $lease;
    }

    public function free(Term $term, string $from): bool
    {
        $start = new \DateTimeImmutable($from);
        $end = $start->modify('+' . $term->days() . ' days');
        foreach ($this->leases as $lease) {
            if ($lease->state() === 'ended') {
                continue;
            }
            $heldFrom = new \DateTimeImmutable($lease->from);
            $heldEnd = $heldFrom->modify('+' . $lease->term->days() . ' days');
            if ($start < $heldEnd && $heldFrom < $end) {
                return false;
            }
        }

        return true;
    }

    /** @return list<Lease> */
    public function leases(): array
    {
        return $this->leases;
    }

    public function taken(): int
    {
        return count(array_filter($this->leases, static fn (Lease $l): bool => $l->state() !== 'ended'));
    }

    public function billing(): int
    {
        $sum = 0;
        foreach ($this->leases as $lease) {
            if ($lease->state() === 'ended') {
                continue;
            }
            $sum += $lease->priceCents();
        }

        return $sum;
    }

    public function nextOpening(): ?string
    {
        $latest = null;
        foreach ($this->leases as $lease) {
            if ($lease->state() === 'ended') {
                continue;
            }
            $ends = $lease->endsOn();
            if ($latest === null || $ends > $latest) {
                $latest = $ends;
            }
        }

        return $latest;
    }
}

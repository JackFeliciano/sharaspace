<?php

declare(strict_types=1);

namespace Sharaspace\Home;

/**
 * A lease ties one tenant to one space for one term. It holds the price the space
 * carried the day the tenant took it, so a later change to the rate cannot
 * rewrite a signed bill.
 */
final class Lease
{
    public function __construct(
        public readonly string $reference,
        public readonly Space $space,
        public readonly string $tenant,
        public readonly Term $term,
        public readonly string $from,
        private int $priceCents,
        private string $state = 'signed',
    ) {
        if (!$space->isOpen()) {
            throw new \RuntimeException('a closed space takes no lease');
        }
    }

    public function state(): string
    {
        return $this->state;
    }

    public function priceCents(): int
    {
        return $this->priceCents;
    }

    public function endsOn(): string
    {
        $end = new \DateTimeImmutable($this->from);
        $end = $end->modify('+' . $this->term->days() . ' days');

        return $end->format('Y-m-d');
    }

    public function end(): void
    {
        if ($this->state === 'ended') {
            throw new \RuntimeException('an ended lease stays ended');
        }
        $this->state = 'ended';
    }
}

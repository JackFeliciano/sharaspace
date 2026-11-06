<?php

declare(strict_types=1);

namespace Sharaspace\App;

use Sharaspace\Home\Sample;

/**
 * The front controller. Routes, then renders. Every figure comes out of the sample
 * diaries, so the page and the health answer cannot drift apart.
 */
final class Front
{
    public function __construct(private readonly string $base = '/')
    {
    }

    public function run(): void
    {
        $router = new Router($this->base);
        $views = new Views($this->base);

        $router->get('/', static fn (): array => [200, [
            'html' => $views->page(new Sample()),
        ]]);
        $router->get('/health', static fn (): array => [200, ['json' => [
            'ok' => true,
            'rooms' => (new Sample())->count(),
            'bookings' => (new Sample())->taken(),
            'takings_cents' => (new Sample())->takings(),
        ]]]);

        [$status, $body] = $router->handle(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
        );

        http_response_code($status);
        if (isset($body['json'])) {
            header('Content-Type: application/json');
            echo json_encode($body['json'], JSON_UNESCAPED_SLASHES), "\n";

            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        if (isset($body['html'])) {
            echo $body['html'];

            return;
        }
        echo $views->missing();
    }
}

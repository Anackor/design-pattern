<?php

namespace App\Tests\Functional\Support;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

/**
 * JsonHttpKernelTestCase provides a small, dependency-free entry point for
 * functional HTTP tests.
 *
 * BrowserKit is not installed in this repository today, so this helper drives
 * the Symfony kernel directly. That still gives us a valuable functional layer:
 * routing, controller wiring, validators, subscribers and framework exception
 * handling all participate in the request.
 */
abstract class JsonHttpKernelTestCase extends KernelTestCase
{
    protected function setTestService(string $id, object $service): void
    {
        $this->bootFunctionalKernel();
        static::getContainer()->set($id, $service);
    }

    protected function requestJson(string $method, string $uri, array|string|null $payload = null, array $query = []): Response
    {
        $content = is_array($payload)
            ? json_encode([] === $payload ? new \stdClass() : $payload, JSON_THROW_ON_ERROR)
            : ($payload ?? '');

        return $this->request($method, $uri, $query, $content, [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, string> $server
     */
    protected function request(string $method, string $uri, array $query = [], string $content = '', array $server = []): Response
    {
        $kernel = static::$booted ? static::$kernel : static::bootKernel([
            'environment' => 'test',
            'debug' => true,
        ]);
        \assert(null !== $kernel);

        $request = Request::create($uri, $method, $query, [], [], $server, $content);
        $response = $kernel->handle($request, HttpKernelInterface::MAIN_REQUEST, true);

        if ($kernel instanceof TerminableInterface) {
            $kernel->terminate($request, $response);
        }

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeJson(Response $response): array
    {
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }

    private function bootFunctionalKernel(): void
    {
        if (static::$booted) {
            return;
        }

        static::bootKernel([
            'environment' => 'test',
            'debug' => true,
        ]);
    }
}

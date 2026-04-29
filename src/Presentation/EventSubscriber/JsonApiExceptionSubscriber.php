<?php

namespace App\Presentation\EventSubscriber;

use App\Presentation\Http\ApiResponseFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * JsonApiExceptionSubscriber turns framework-level HTTP exceptions into small,
 * machine-readable JSON responses.
 *
 * This project is API-first, so returning an HTML error page for malformed
 * input teaches the wrong lesson. The important part here is the HTTP
 * contract: callers should receive a stable JSON payload and the right status
 * code when the boundary rejects the request.
 *
 * The subscriber stays intentionally narrow. It normalizes HTTP exceptions such
 * as bad requests, while leaving non-HTTP failures visible for dedicated
 * handling in controllers or future cross-cutting policies.
 */
final class JsonApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private ApiResponseFactory $apiResponseFactory) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $requestPath = $event->getRequest()->getPathInfo();

        if (str_starts_with($requestPath, '/_profiler') || str_starts_with($requestPath, '/_wdt')) {
            return;
        }

        $throwable = $event->getThrowable();
        if (!$throwable instanceof HttpExceptionInterface) {
            return;
        }

        $statusCode = $throwable->getStatusCode();
        $message = '' !== trim($throwable->getMessage())
            ? $throwable->getMessage()
            : (Response::$statusTexts[$statusCode] ?? 'HTTP error');

        $response = $this->apiResponseFactory->httpError($statusCode, $message);
        $response->headers->add($throwable->getHeaders());

        $event->setResponse($response);
    }
}

<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Silex;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Default exception handler.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExceptionHandler implements EventSubscriberInterface
{
    private $debug;
    private $charset;

    public function __construct($debug, $charset = 'UTF-8')
    {
        $this->debug = $debug;
        $this->charset = $charset;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $response = new Response();
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        }

        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::createFromThrowable($exception);
        }

        $message = $this->escapeContent($exception->getMessage());

        if (!$this->debug) {
            if ($exception->getStatusCode() === Response::HTTP_NOT_FOUND) {
                $message = 'Sorry, the page you are looking for could not be found.';
            } else {
                $message = 'Whoops, looks like something went wrong.';
            }
        }

        $response->setContent($message);

        $event->setResponse($response);
    }

    private function escapeContent(string $str): string
    {
        return htmlspecialchars($str, \ENT_COMPAT | \ENT_SUBSTITUTE, $this->charset);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', -255]];
    }
}

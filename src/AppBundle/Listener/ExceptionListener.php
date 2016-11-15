<?php

namespace AppBundle\Listener;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class ExceptionListener implements LoggerAwareInterface
{

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();

        // Customize your response object to display the exception details
        $response = new JsonResponse();

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
        } elseif ($exception instanceof AuthenticationCredentialsNotFoundException) {
            $response->setStatusCode(401);
        } else {
            $response->setStatusCode(500);
        }

        $data = array(
            'errorCode' => $response->getStatusCode(),
            'errorMessage' => $exception->getMessage(),
        );

        if ($data['errorCode'] >= 500) {
            $data['errorMessage'] = 'Internal server error.'. $exception->getMessage();
        }

        $response->setData($data);

        // Send the modified response object to the event
        $event->setResponse($response);
    }
}

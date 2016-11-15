<?php

namespace AppBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecureListener implements EventSubscriberInterface
{
    private $tokenStorage;
    private $authorizationChecker;

    public function __construct(TokenStorage $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        // Get the annotation configuration
        if (!$configuration = $request->attributes->get('_secure')) {
            return;
        }

        if (!$this->tokenStorage) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->tokenStorage->getToken()) {
            $this->throwException($configuration);
        }

        if (!is_object($user = $token->getUser())) {
            $this->throwException($configuration);
        }

        foreach ($configuration->getValue() as $requiredRole) {
            if ($this->authorizationChecker->isGranted($requiredRole)) {
                return; // The user has at least one of the required roles, access granted.
            }
        }

        // Fallthru, deny the user.
        $this->throwException($configuration);
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::CONTROLLER => 'onKernelController');
    }

    protected function throwException($configuration)
    {
        $exceptionClass = $configuration->getException();

        if ($exceptionMessage = $configuration->getMessage()) {
            $exception = new $exceptionClass($exceptionMessage);
        } else {
            $exception = new $exceptionClass();
        }

        throw $exception;
    }
}

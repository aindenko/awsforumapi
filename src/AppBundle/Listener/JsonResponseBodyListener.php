<?php

namespace AppBundle\Listener;

use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class JsonResponseBodyListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    public function __construct($serializeNull)
    {
        $this->serializeNull = $serializeNull;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        return;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $parameters = $event->getControllerResult();

        if (!$annotationConfiguration = $request->attributes->get('_jsonresponse')) {
            return $parameters;
        }

        $httpStatusCode = $annotationConfiguration->getValue();
        $httpResponseHeaders = $annotationConfiguration->getHeaders();
        $httpResponseHeaders['Content-Type'] = 'application/json';

        // Prepare for JMS Serializer
        $jmsSerializerContext = SerializationContext::create()->setSerializeNull($this->serializeNull);

        if ($jmsSerializerGroups = $annotationConfiguration->getSerializerGroups()) {
            if ($this->hasElevatedPermissions()) {
                if (!is_array($jmsSerializerGroups)) {
                    // This way we will handle both syntaxes:
                    //
                    // * @JsonResponse(serializerGroups="list")
                    // * @JsonResponse(serializerGroups={"list"})
                    $jmsSerializerGroups = array($jmsSerializerGroups);
                }
                $jmsSerializerGroups[] = 'admin_only';
            }
            $jmsSerializerContext->setGroups($jmsSerializerGroups);
        }

        $response = new Response(
            $this->container->get('jms_serializer')->serialize($parameters, 'json', $jmsSerializerContext),
            $httpStatusCode,
            $httpResponseHeaders
        );

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array('onKernelController', -128),
            KernelEvents::VIEW => 'onKernelView',
        );
    }

    protected function hasElevatedPermissions()
    {
        if (!$this->container->get('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return false;
        }

        if (!is_object($token->getUser())) {
            // e.g. anonymous authentication
            return false;
        }

        return $this->container->get('security.authorization_checker')->isGranted(array());
    }
}

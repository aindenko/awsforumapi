<?php

namespace AppBundle\Listener;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class JsonRequestBodyListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();

        if (!count($request->request->all())
            && in_array($method, array('POST', 'PUT', 'PATCH', 'DELETE'))
        ) {
            $contentType = $request->headers->get('Content-Type');

            $format = null === $contentType
                ? $request->getRequestFormat()
                : $request->getFormat($contentType);

            $content = $request->getContent();

            if ('json' !== $format) {
                return;
            }

            if (empty($content)) {
                return;
            }

            $data = json_decode($content, true);

            if (json_last_error() == JSON_ERROR_NONE && is_array($data)) {
                $request->request = new ParameterBag($data);
            }
        }
    }

    private function isNotAnEmptyDeleteRequestWithNoSetContentType($method, $content, $contentType)
    {
        return false === ('DELETE' === $method && empty($content) && null === $contentType);
    }
}

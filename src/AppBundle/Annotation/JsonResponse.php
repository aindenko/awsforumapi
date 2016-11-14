<?php

namespace AppBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 */
class JsonResponse extends ConfigurationAnnotation
{
    protected $value = 200;
    protected $headers = array();
    protected $serializerGroups = array();

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = (int) $value;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getSerializerGroups()
    {
        return $this->serializerGroups;
    }

    public function setSerializerGroups($serializerGroups)
    {
        $this->serializerGroups = $serializerGroups;
    }

    public function getAliasName()
    {
        return 'jsonresponse';
    }

    public function allowArray()
    {
        return false;
    }
}

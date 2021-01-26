<?php

namespace EdSukharev\App\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class RequestHeaders implements ParamConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $parameterName = lcfirst(str_replace('-', '', $configuration->getName()));
        $headerName = trim(preg_replace('/([A-Z])/', '-$1', $parameterName), '-');

        if (!$request->headers->has($headerName) && !$configuration->isOptional()) {
            throw new MissingHeaderException($headerName);
        }
        $object = $request->headers->get($headerName);
        if (in_array(strtolower($configuration->getClass()), ['int', 'integer'])) {
            $object = (int)$object;
        }
        if (in_array(strtolower($configuration->getClass()), ['str', 'string'])) {
            $object = (string)$object;
        }
        if (in_array(strtolower($configuration->getClass()), ['bool', 'boolean'])) {
            $object = (bool)$object;
        }

        $request->attributes->set($parameterName, $object);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return 'request_header_converter' === $configuration->getConverter();
    }
}

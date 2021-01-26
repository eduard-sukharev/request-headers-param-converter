<?php

namespace EdSukharev\App\ParamConverter;

class MissingHeaderException extends \RuntimeException
{
    const DEFAULT_MESSAGE_TEMPLATE = 'Header %s is missing';

    public function __construct($headerName, $code = 400, $message = '')
    {
        if (!$message) {
            $message = sprintf(self::DEFAULT_MESSAGE_TEMPLATE, $headerName);
        }

        parent::__construct($message, $code);
    }
}

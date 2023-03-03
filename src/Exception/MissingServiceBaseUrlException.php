<?php

namespace RedSnapper\OneKey\Exception;

class MissingServiceBaseUrlException extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            'Could not initialise PHPCAS: service-base-url is not configured and failed to get a default'
        );
    }
}
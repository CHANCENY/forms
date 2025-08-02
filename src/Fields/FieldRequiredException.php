<?php

namespace Simp\Fields;
use Exception;

class FieldRequiredException extends Exception
{

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
        parent::__construct($string);
    }
}
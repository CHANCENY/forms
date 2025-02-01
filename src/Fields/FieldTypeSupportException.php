<?php

namespace Simp\Fields;

class FieldTypeSupportException extends \Exception
{

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
        parent::__construct($string);
    }
}
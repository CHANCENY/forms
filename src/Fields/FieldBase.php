<?php

namespace Simp\Fields;

abstract class FieldBase implements FieldInterface
{
    public function __construct(array $field, string $request_method, array $post = [], array $params = [], array $files = [])
    {
    }

}
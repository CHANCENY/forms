<?php

namespace Simp\Fields;

abstract class FieldBase implements FieldInterface
{
    public function __construct(array $field, string $request_method, array $post = [], array $params = [], array $files = [])
    {
    }

    protected function findInnerFieldValue(string $field_name, $values) {
        foreach ($values as $key=>$value) {

            if (is_array($value)) {
                return $this->findInnerFieldValue($field_name,$value);
            }
            elseif ($key === $field_name) {
                return $value;
            }
        }
        return null;
    }
}
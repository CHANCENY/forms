<?php

namespace Simp\Fields;

/**
 * Interface DisplayHandlerInterface
 *
 * Defines a contract for handling the display of fields based on their type
 * and associated metadata.
 */
interface DisplayHandlerInterface
{
    /**
     * Renders and returns a formatted display of a field based on its type and context.
     *
     * @param string $field_type The type of the field to be displayed.
     * @param array $field The field data to be processed and displayed.
     * @param array $definitions Additional definitions or metadata related to the field.
     * @param array $context Contextual information that can influence how the field is displayed.
     *
     * @return string The rendered output of the field as a string.
     */
    public function display(string $field_type, FieldBase $field, array $context): string;

}
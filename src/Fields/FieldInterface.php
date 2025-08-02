<?php

namespace Simp\Fields;

/**
 * All Field need to implement this interface to be considered as field.
 * @class Field interface
 */
interface FieldInterface {

    /**
     * Label of field.
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get field name.
     * @return string
     */
    public function getName(): string;

    /**
     * Get field type.
     * @return string
     */
    public function getType(): string;

    /**
     * Get field id.
     * @return string
     */
    public function getId(): string;

    /**
     * Get class list.
     * @return array
     */
    public function getClassList(): array;

    /**
     * Get required.
     * @return string
     */
    public function getRequired(): string;

    /**
     * Get field options.
     * @return array
     */
    public function getOptions(): array;

    /**
     * Get default values.
     * @return string|int|float|array|bool|null
     */
    public function getDefaultValue(): string|int|float|null|array|bool;

    /**
     * Get field value.
     * @return string|int|float|array|bool|null
     */
    public function getValue(): string|int|float|null|array|bool;

    public function get(string $field_name);

    /**
     * Get field rendered html.
     * @param bool $wrapper
     * @return string
     */
    public function getBuildField(bool $wrapper = true): string;

    public function setError(string $error): void;
    
    public function getError(): string;

    public function getDescription(): string;

    public function __toString(): string;
    public function getField(): array;
}
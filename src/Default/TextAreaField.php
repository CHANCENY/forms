<?php

namespace Simp\Default;

use Simp\Default\FileField;
use Simp\Fields\FieldBase;
use Simp\Fields\FieldRequiredException;
use Simp\Fields\FieldTypeSupportException;

class TextAreaField extends FieldBase
{
    private array $field;
    private array $submission;
    protected string $validation_message;
    public function __construct(array $field, string $request_method, array $post = [], array $params = [], array $files = [])
    {
        parent::__construct($field, $request_method, $post, $params, $files);

        $this->validation_message = '';

        $supported_field_type = ['textarea'];

        if (!in_array($field['type'], $supported_field_type)) {
            throw new FieldTypeSupportException("Field type '{$field['type']}' is not supported with this class ".FileField::class);
        }

        $required = ['label', 'name', 'type'];

        foreach ($required as $field_key) {

            if (!isset($field[$field_key])) {
                throw new FieldRequiredException("Field key {$field_key} is required");
            }
        }

        $this->field = $field;

        if ($request_method === 'POST') {

            $value = $post[$field['name']] ?? null;

            if ($value !== null) {
                if (!empty($this->field['sanitize'])) {
                    if (is_array($value)) {
                        foreach ($value as $k=>$sub_value) {
                            $value[$k] = htmlspecialchars(strip_tags($sub_value));
                        }
                    }
                    else {
                        $value = htmlspecialchars(strip_tags($value));
                    }
                }
                $this->submission['value'] = $value;
            }

        }

        if ($request_method === 'GET' && !empty($params)) {

            $value = $params[$field['name']] ?? null;
            if ($value !== null) {
                if (!empty($this->field['sanitize'])) {
                    if (is_array($value)) {
                        foreach ($value as $k=>$sub_value) {
                            $value[$k] = htmlspecialchars(strip_tags($sub_value));
                        }
                    }
                    else {
                        $value = htmlspecialchars(strip_tags($value));
                    }
                }
                $this->submission['value'] = $value;
            }
        }

    }
    public function getLabel(): string
    {
        return $this->field['label'] ?? '';
    }

    public function getName(): string
    {
        return $this->field['name'] ?? '';
    }

    public function getType(): string
    {
        return $this->field['type'] ?? '';
    }

    public function getId(): string
    {
        return $this->field['id'] ?? '';
    }

    public function getClassList(): array
    {
        return $this->field['class'] ?? [];
    }

    public function getRequired(): string
    {
        return !empty($this->field['required']) ? 'required' : '';
    }

    public function getOptions(): array
    {
        return $this->field['options'] ?? [];
    }

    public function getDefaultValue(): string|int|float|null|array|bool
    {

        return $this->field['default_value'] ?? null;
    }

    public function getValue(): string|int|float|null|array|bool
    {
        return $this->submission['value'] ?? $this->getDefaultValue();
    }

    public function getBuildField(bool $wrapper = true): string
    {
        $class = implode(' ', $this->getClassList());
        $options = null;
        foreach ($this->getOptions() as $key=>$option) {
            $options .= $key . '="' . $option . '" ';
        }

        if ($wrapper) {
            return <<<FIELD
<div class="field-wrapper field--{$this->getName()} js-form-field-{$this->getName()}">
    <label for="{$this->getId()}">{$this->getLabel()}</label>
    <{$this->getType()}
    name="{$this->getName()}" 
    id="{$this->getId()}" 
    class="{$class} js-form-field-{$this->getName()} field-field--{$this->getName()} js-form-field-{$this->getName()}"
      {$options}/>{$this->getValue()}</{$this->getType()}>
     <span class="field-description">{$this->getDescription()}</span>
     <span class="field-message message-{$this->getName()}">{$this->validation_message}</span>
</div>
FIELD;
        }

        return <<<FIELD
<label for="{$this->getId()}">{$this->getLabel()}
 <{$this->getType()}
    name="{$this->getName()}" 
    id="{$this->getId()}" 
    class="{$class} js-form-field-{$this->getName()} field-field--{$this->getName()} js-form-field-{$this->getName()}"
    {$options}>{$this->getValue()}</{$this->getType()}>
     <span class="field-description">{$this->getDescription()}</span>
     <span class="field-message message-{$this->getName()}">{$this->validation_message}</span>
</label>
FIELD;
    }

    public function setError(string $error): void
    {
        $this->validation_message = $error;
    }

    public function getDescription(): string
    {
        return $this->field['description'] ?? '';
    }

    public function __toString(): string
    {
        return $this->getBuildField();
    }

    public function get(string $field_name)
    {
        return $this->getValue();
    }

    public function getField(): array
    {
       return $this->field;
    }
}
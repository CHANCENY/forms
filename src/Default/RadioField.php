<?php

namespace Simp\Default;

use Simp\Fields\FieldBase;
use Simp\Fields\FieldRequiredException;
use Simp\Fields\FieldTypeSupportException;

class RadioField extends FieldBase
{
    private array $field;
    private array $submission;
    protected string $validation_message;

    public function __construct(array $field, string $request_method, array $post = [], array $params = [], array $files = [])
    {
        parent::__construct($field, $request_method, $post, $params, $files);

        $supported_field_type = [
            'radio',
        ];

        $required = [
            'name',
            'id',
            'radios'
        ];

        $this->validation_message = '';

        foreach ($required as $field_key) {
            if (!array_key_exists($field_key, $field)) {
                throw new FieldRequiredException('Field "' . $field_key . '" is required.');
            }
        }

        if (!in_array($field['type'], $supported_field_type)) {
            throw new FieldTypeSupportException('Field "' . $field['type'] . '" is not supported type.');
        }

        $this->field = $field;

        if ($request_method === 'POST') {

            $value = $post[$field['name']] ?? null;
            if (!empty($field['required']) && empty($value) && empty($field['default_value'])) {
                $this->validation_message = "This input field is mandatory.";
            }

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
        return $this->field['default_value'] ?? '';
    }

    public function getValue(): string|int|float|null|array|bool
    {
        return !empty($this->submission['value']) ? $this->submission['value'] : $this->field['default_value'] ?? '';
    }

    public function get(string $field_name): float|int|bool|array|string|null
    {
        return $this->getValue();
    }

    public function getBuildField(bool $wrapper = true): string
    {
        $class = implode(' ', $this->getClassList());
        $options = null;
        foreach ($this->getOptions() as $key=>$option) {
            $options .= $key . '="' . $option . '" ';
        }

        $radios = [];
        $values = $this->getValue();
        $values = is_array($values) ? $values : [$values];
        
        foreach ($this->field['radios'] ?? [] as $radio) {

            if (!empty($this->field['limit']) && $this->field['limit'] > 1) {
                $id = "field-edit-".str_replace(' ','-',$radio);
                $class = $id . '-option';
                if (in_array($radio, $values)) {
                    $radios[] = "<div class='radio-option-field'><input type='radio' name='{$this->getName()}[]' id='{$id}' value='{$radio}' class='{$class}' checked/><label>{$radio}</label></div>";
                }
                else {
                    $radios[] = "<div class='radio-option-field'><input type='radio' name='{$this->getName()}[]' id='{$id}' value='{$radio}' class='{$class}'/><label>{$radio}</label></div>";
                }
            }
            else {
                $id = "field-edit-".str_replace(' ','-',$radio);
                $class = $id . '-option';
                if (in_array($radio, $values)) {
                    $radios[] = "<div class='radio-option-field'><input type='radio' name='{$this->getName()}' id='{$id}' value='{$radio}' class='{$class}' checked/><label>{$radio}</label></div>";
                }
                else {
                    $radios[] = "<div class='radio-option-field'><input type='radio' name='{$this->getName()}' id='{$id}' value='{$radio}' class='{$class}'/><label>{$radio}</label></div>";
                }
            }

        }

        $line = implode('', $radios);
        return <<<FIELD
<div class="field-wrapper field--{$this->getName()} js-form-field-{$this->getName()}">
    <label for="{$this->getId()}">{$this->getLabel()}</label>
     {$line}
     <span class="field-description">{$this->getDescription()}</span>
     <span class="field-message message-{$this->getName()}">{$this->validation_message}</span>
</div>
FIELD;

    }

    public function __toString(): string
    {
        return $this->getBuildField();
    }

    public function setError(string $error): void
    {
        $this->validation_message = $error;
    }

    public function getDescription(): string
    {
        return $this->field['description'] ?? '';
    }

    public function getField(): array
    {
        return $this->field;
    }
}
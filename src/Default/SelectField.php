<?php

namespace Simp\Default;

use Simp\Fields\FieldBase;
use Simp\Fields\FieldRequiredException;
use Simp\Fields\FieldTypeSupportException;

class SelectField extends FieldBase
{
    private array $field;
    private array $submission;
    protected string $validation_message;

    public function __construct(array $field, string $request_method, array $post = [], array $params = [], array $files = [])
    {
        parent::__construct($field, $request_method, $post, $params, $files);

        $this->validation_message = '';

        $supported_field_type = ['select'];

        if (!in_array($field['type'], $supported_field_type)) {
            throw new FieldTypeSupportException("Field type '{$field['type']}' is not supported with this class ".SelectField::class);
        }

        $required = ['label', 'name', 'type', 'option_values'];

        foreach ($required as $field_key) {
            if (!isset($field[$field_key])) {
                throw new FieldRequiredException("Field key {$field_key} is required");
            }
        }

        $this->field = $field;

        if ($request_method === 'POST') {
            $field_name = $field['name'];
            if (str_ends_with($field_name, '[]')) {
                $field_name = substr($field_name, 0, -2);
            }

            $value = $post[$field_name] ?? null;
            if (!is_null($value)) {
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

                if (!empty($this->field['option_values'])) {
                    if (is_array($value)) {
                        foreach ($value as $k=>$sub_value) {
                            if (in_array($sub_value, $this->field['option_values'])) {
                                $value[$k] = htmlspecialchars(strip_tags($sub_value));
                            }
                            else {
                                $this->validation_message = "you have selected value which is not a valid option";
                            }
                        }
                    }
                    elseif (!in_array($value, array_keys($this->field['option_values']))) {
                        $this->validation_message = "you have selected value which is not a valid option";
                    }
                    $this->submission['value'] = $value;
                }
            }
        }

        if ($request_method === 'GET') {
            $value = $params[$field['name']] ?? null;
            if (!is_null($value)) {

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
                if (!empty($this->field['option_values'])) {
                    if (is_array($value)) {
                        foreach ($value as $k=>$sub_value) {
                            if (in_array($sub_value, array_keys($this->field['option_values']))) {
                                $value[$k] = htmlspecialchars(strip_tags($sub_value));
                            }
                            else {
                                $this->validation_message = "you have selected value which is not a valid option";
                            }
                        }
                    }
                    elseif (!in_array($value, array_keys($this->field['option_values']))) {
                        $this->validation_message = "you have selected value which is not a valid option";
                    }
                    $this->submission['value'] = $value;
                }
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
    public function getField(): array
    {
        return $this->field;
    }

    public function getValue(): string|int|float|null|array|bool
    {
        return !empty($this->submission['value']) ? $this->submission['value'] : $this->getDefaultValue();
    }

    public function get(string $field_name)
    {
        return $this->getValue();
    }

    public function getBuildField(bool $wrapper = true): string
    {
        $options = $this->field['option_values'] ?? [];
        $values = $this->getValue();
        $values = is_array($values) ? $values : [$values];
        $option_html = [];
        foreach ($options as $key=>$option) {
            if (in_array($option, $values)) {
                $option_html[] = '<option value="' . $option . '" selected>' . $option . '</option>';
            }
            else {
                $option_html[] = '<option value="' . $option . '">' . $option . '</option>';
            }
        }

        $option_html = implode('\n', $option_html);
        $class = implode(' ', $this->getClassList());

        $name = $this->getName();
        $class_name = $this->getName();
        if (!empty($this->field['limit']) && $this->field['limit'] > 1) {
            $name = $name . '[]';
            $this->field['options'][] = 'multiple';
        }

        $options = implode('', $this->getOptions());

        if ($wrapper === true) {
            return <<<FIELD_HTML
<div class="field-wrapper field--{$class_name} js-form-field-{$class_name}">
    <label for="{$this->getId()}">{$this->getLabel()}</label>
    <select name="{$name}" 
    id="{$this->getId()}"
    class="{$class} js-form-field-{$class_name} field-field--{$class_name} js-form-field-{$class_name}"
     {$options}>
    <option value="">Select...</option>
    {$option_html}
</select>
     <span class="field-description">{$this->getDescription()}</span>
     <span class="field-message message-{$class_name}">{$this->validation_message}</span>
</div>
FIELD_HTML;
        }
        
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
}


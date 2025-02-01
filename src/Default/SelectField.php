<?php

namespace Simp\Default;

use Simp\Fields\FieldBase;
use Simp\Fields\FieldRequiredException;
use Simp\Fields\FieldTypeSupportException;

class SelectField extends FieldBase
{
    private array $field;
    private array $submission;
    private string $validation_message;
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
                $this->submission['value'] = $value;
            }
        }

        if ($request_method === 'GET') {
            $value = $params[$field['name']] ?? null;
            if (!is_null($value)) {
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
        return !empty($this->submission['value']) ? $this->submission['value'] : $this->getDefaultValue();
    }

    public function getBuildField(bool $wrapper = true): string
    {
        $options = $this->field['option_values'] ?? [];
        $value = $this->getValue();
        $option_html = [];
        foreach ($options as $key=>$option) {

            $added = false;
            if (is_array($value)) {
                foreach ($value as $item) {

                    if ($item === $key) {
                        $option_html[] = "<option value='{$key}' selected>{$option}</option>";
                        $added = true;
                    }
                }
            }
            elseif (  is_string($value)) {
                if ($value === $key) {
                    $option_html[] = "<option value='{$key}' selected>{$option}</option>";
                    $added = true;
                }
            }

            if ($added === false) {
                $option_html[] = "<option value='{$key}'>{$option}</option>";
            }
        }

        $option_html = implode('\n', $option_html);
        $class = implode(' ', $this->getClassList());
        $options = implode('', $this->getOptions());


        if ($wrapper === true) {
            return <<<FIELD_HTML
<div class="field-wrapper field--{$this->getName()} js-form-field-{$this->getName()}">
    <label for="{$this->getId()}">{$this->getLabel()}</label>
    <select name="{$this->getName()}" 
    id="{$this->getId()}"
    class="{$class} js-form-field-{$this->getName()} field-field--{$this->getName()} js-form-field-{$this->getName()}"
     {$options}>
    >
    <option value="">Select...</option>
    {$option_html}
</select>
     <span class="field-description">{$this->getDescription()}</span>
     <span class="field-message message-{$this->getName()}">{$this->validation_message}</span>
</div>
FIELD_HTML;
        }

        return <<<FIELD_HTML
 <label for="{$this->getId()}">{$this->getLabel()}
  <select name="{$this->getName()}" 
    id="{$this->getId()}"
    class="{$class} js-form-field-{$this->getName()} field-field--{$this->getName()} js-form-field-{$this->getName()}"
     {$options}>
    >
    <option value="">Select...</option>
    {$option_html}
</select>
     <span class="field-description">{$this->getDescription()}</span>
     <span class="field-message message-{$this->getName()}">{$this->validation_message}</span>
 </label>
FIELD_HTML;


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
<?php

namespace Simp\Default;

use Simp\Fields\FieldBase;
use Simp\Fields\FieldRequiredException;
use Simp\Fields\FieldTypeSupportException;

class FileField extends FieldBase
{

    private array $field;
    private array $submission;
    private string $validation_message;
    public function __construct(array $field, string $request_method, array $post = [], array $params = [], array $files = [])
    {
        parent::__construct($field, $request_method, $post, $params, $files);

        $this->validation_message = '';

        $supported_field_type = ['file'];

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
            $field_name = $field['name'];
            if (str_ends_with($field_name, '[]')) {
                $field_name = substr($field_name, 0, -2);
            }

            $value = $files[$field_name] ?? null;
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
       return !empty($this->field['value']) ? $this->field['value'] : $this->submission['value'] ?? null;
    }

    public function getValue(): string|int|float|null|array|bool
    {
        return $this->submission['value'] ?? null;
    }

    public function get(string $field_name)
    {
        return $this->getValue();
    }

    private function formatSize(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $factor = 1024;
        $i = 0;

        while ($size >= $factor && $i < count($units) - 1) {
            $size /= $factor;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function getBuildField(bool $wrapper = true): string
    {
        $values = $this->getValue();
        $index = array_search('multiple', $this->getOptions());

        $default_values = [];
        if ($index !== false && $this->getOptions()[$index] === 'multiple' && !empty($values['name']) && is_array($values['name'])) {

            for ($i = 0; $i < count($values['name']); $i++) {
                $default_values[] = [
                    'name' => $values['name'][$i],
                    'size' => $this->formatSize($values['size'][$i]),
                    'type' => $values['type'][$i],
                ];
            }
        }
        elseif(!empty($values['name'])) {
            $default_values[] = [
                'name' => $values['name'],
                'size' => $this->formatSize($values['size']),
                'type' => $values['type'],
            ];
        }

        $class = implode(' ', $this->getClassList());
        $options = implode(' ', $this->getOptions());
        $class_name = trim($this->getName(),']');
        $class_name = trim($class_name, '[');

        $uploaded_files = null;
        foreach ($default_values as $default_value) {
            $uploaded_files .= <<<UPLOAD
<div class="file-wrapper-upload">
  <div>
  <strong>File: </strong>
  <span>{$default_value['name']} &nbsp;&nbsp;</span>
  <span>{$default_value['size']} &nbsp;&nbsp;</span>
  <span>{$default_value['type']}</span>
  </div>
</div>
UPLOAD;
        }

        if ($wrapper) {
            return <<<FIELD
<div class="field-wrapper field--{$class_name} js-form-field-{$class_name}">
    <label for="{$this->getId()}">{$this->getLabel()}</label>
    <input type="{$this->getType()}" 
    name="{$this->getName()}" 
    id="{$this->getId()}" 
    class="{$class} js-form-field-{$class_name} field-field--{$class_name} js-form-field-{$class_name}"
      {$options}/>
     <span class="field-description">{$this->getDescription()}</span>
     <span class="field-message message-{$class_name}">{$this->validation_message}</span>
     {$uploaded_files}
</div>
FIELD;
        }
        return <<<FIELD
<label for="{$this->getId()}">{$this->getLabel()}
 <input type="{$this->getType()}" 
    name="{$this->getName()}" 
    id="{$this->getId()}" 
    class="{$class} js-form-field-{$this->getName()} field-field--{$this->getName()} js-form-field-{$this->getName()}"
      {$options}/>
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
    public function getField(): array
    {
        return $this->field;
    }
}
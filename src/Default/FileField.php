<?php

namespace Simp\Default;

use Simp\Core\modules\files\entity\File;
use Simp\Core\modules\files\helpers\FileFunction;
use Simp\Fields\FieldBase;
use Simp\Fields\FieldRequiredException;
use Simp\Fields\FieldTypeSupportException;

class FileField extends FieldBase
{

    private array $field;
    private array $submission;
    protected string $validation_message;
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

            $hidden_name = "field-". $this->field['name']. "_hidden";
            if (isset($_POST[$hidden_name])) {
                $this->submission['value'] = $_POST[$hidden_name];
            }
            else {
                $hidden_name = $this->field['name']. "_hidden";
                if (isset($_POST[$hidden_name])) {
                    $this->submission['value'] = $_POST[$hidden_name];
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
       return !empty($this->field['id']) ? $this->field['id'] : 'field-'.$this->getName();
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
       return !empty($this->field['value']) ? $this->field['value'] : $this->submission['value'] ??
           $this->field['default_value'] ?? null;
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
        $class = implode(' ', $this->getClassList());
        $options = implode(' ', $this->getOptions());
        $class_name = trim($this->getName(),']');
        $class_name = trim($class_name, '[');
        $name = $this->getName();
        $multiple = null;

        $defaults = $this->getDefaultValue();
        $defaults = json_encode($defaults,JSON_PRETTY_PRINT);
        $settings = $this->field;
        unset($settings['handler']);
        $settings = json_encode($settings,JSON_PRETTY_PRINT);

        if (!empty($this->field['limit']) && $this->field['limit'] > 1) {
            $name = $name . '[]';
            $multiple = 'multiple';
        }

        $wrapper_id = uniqid("file-field-wrapper");

        $styles = <<<STYLES
<style>
  .spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    animation: spin 0.8s linear infinite;
    display: inline-block;
    margin-left: 5px;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .$wrapper_id { margin: 5px 0; }
</style>
STYLES;

        $field_html = <<<FIELD
<div class="field-wrapper field--{$class_name} js-form-field-{$class_name}" id="{$wrapper_id}">
 <label for="{$this->getId()}">{$this->getLabel()}</label>
    <input type="{$this->getType()}" 
    name="{$name}" 
    id="{$this->getId()}" 
    {$multiple}
    class="{$class} js-form-field-{$class_name} field-field--{$class_name} js-form-field-{$class_name}"
      {$options}/>
      <input type="hidden" id="{$this->getId()}-hidden" name="{$this->getId()}_hidden" value="[]">
       <div class="preview"></div>
     <span class="field-description">{$this->getDescription()}</span>
     <span class="field-message message-{$class_name}">{$this->validation_message}</span>
     <noscript style="display: none;">{$defaults}</noscript>
     <script type="application/json" class="settings" style="display: none;">{$settings}</script>
</div>
FIELD;

        $script = file_get_contents(__DIR__. "/../../assets/upload.js");

        $script_html = <<<SCRIPT
       <script>
       (function() {
           
               const fileFieldWrapper = document.querySelector('#{$wrapper_id}'); 
               {$script}
       })();
       </script>
SCRIPT;

        return $field_html . $script_html . $styles;
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
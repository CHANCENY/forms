<?php

namespace Simp\Default;

use Simp\Fields\FieldBase;
use Simp\Fields\FieldRequiredException;
use Simp\Fields\FieldTypeSupportException;

class ConditionalField extends FieldBase
{
    private array $field;
    private array $submission;
    private string $validation_message;

    public function __construct(array $field, string $request_method, array $post = [], array $params = [], array $files = [])
    {
        parent::__construct($field, $request_method, $post, $params, $files);

        $supported_field_type = [
            'conditional',
        ];

        $required = [
            'label',
            'id',
            'inner_field',
            'name',
            'conditions'
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

        // Map the conditions
        $conditions = $field['conditions'];
        foreach ($conditions as $key=>$condition) {
            $inner_field = $field['inner_field'][$key] ?? [];
            if (!empty($inner_field)) {
                $receiver_field = $field['inner_field'][$condition['receiver_field']] ?? [];
                $receiver_field['class'][] = "field-hidden-".$receiver_field['name'];
                $field['inner_field'][$condition['receiver_field']] = $receiver_field;
            }
        }

        if (!empty($field['inner_field'])) {
            $files = $_FILES;
            $post = $_POST;
            $params = $_GET;
            $method = $_SERVER['REQUEST_METHOD'];

            foreach ($field['inner_field'] as $name=>$inner_field) {
                $handler = $inner_field['handler'] ?? BasicField::class;
                $object = new $handler($inner_field, $method, $post, $params, $files);
                $field['inner_field'][$name] = $object;
            }
        }

        $this->field = $field;

        if ($request_method === 'POST') {

            $value = $post[$field['name']] ?? null;

            if ($value !== null) {
                $this->submission['value'] = $value;
            }

        }

        if ($request_method === 'GET' && !empty($params)) {

            $value = $params[$field['name']] ?? null;
            if ($value !== null) {
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
        if (!empty($this->field['inner_field'])) {
            $values = [];
            foreach ($this->field['inner_field'] as $key=>$inner_field) {
                if ($inner_field instanceof FieldBase) {
                    $values[$key] = $inner_field->getValue();
                }
            }
            return $values;
        }
        return !empty($this->submission['value']) ? $this->submission['value'] : $this->field['default_value'] ?? '';
    }

    public function get(string $field_name)
    {
        $values = $this->getValue();
        return $this->findInnerFieldValue($field_name,$values);

    }

    public function getBuildField(bool $wrapper = true): string
    {
        $class = implode(' ', $this->getClassList());
        $options = null;
        foreach ($this->getOptions() as $key=>$option) {
            $options .= $key . '="' . $option . '" ';
        }

        $fields_html = null;
        foreach ($this->field['inner_field'] as $field) {
            if ($field instanceof FieldBase) {
                $fields_html .= $field->__toString(). "\n";
            }
        }

        $conditions = $this->field['conditions'] ?? [];
        $script_condition = null;
        foreach ($conditions as $key=>$condition) {
            $event = $condition['event'] ?? '';
            $receiver_field = "field-hidden-". $condition['receiver_field'] ?? '';
            $script_condition .= <<<CONDITIONAL_SCRIPT
<script type="text/javascript">
(function() {
    const field_trigger = document.getElementById('$key');
     let hidden_field = document.querySelector('.$receiver_field');
     if (hidden_field.parentElement.classList.contains('field-wrapper')) {
         hidden_field = hidden_field.parentElement;
     }
     hidden_field.style.display = 'none';
    if (field_trigger) {
        field_trigger.addEventListener('$event',(e)=>{
            e.preventDefault();
            const value = e.target.value || field_trigger.checked || null;
            let flag = false;
            if (value) {
               flag = true;
            }
            
            if (flag) {
                hidden_field.removeAttribute('style');
            }else {
                hidden_field.style.display = 'none';
            }
        })
    }
})();
</script>
CONDITIONAL_SCRIPT;
        }

        if ($wrapper) {
            return <<<FIELD
<div class="field-wrapper field--{$this->getName()} js-form-field-{$this->getName()}">
       <fieldset name="{$this->getName()}" 
       class="{$class} js-form-field-{$this->getName()} field-field--{$this->getName()} js-form-field-{$this->getName()}"
       {$options}
       >
        <legend>{$this->getLabel()}</legend>
         {$fields_html}
      </fieldset>
      <span class="field-description">{$this->getDescription()}</span>
      <span class="field-message message-{$this->getName()}">{$this->validation_message}</span>
      {$script_condition}
</div>
FIELD;
        }

        return <<<FIELD
 <fieldset name="{$this->getName()}" 
       class="{$class} js-form-field-{$this->getName()} field-field--{$this->getName()} js-form-field-{$this->getName()}"
       {$options}
       >
        <legend>{$this->getLabel()}</legend>
         {$fields_html}
      </fieldset>
     <span class="field-description">{$this->getDescription()}</span>
     <span class="field-message message-{$this->getName()}">{$this->validation_message}</span>
{$script_condition}
FIELD;
        return "";
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
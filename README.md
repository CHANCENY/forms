# Form Library

## Overview
This PHP-based form library simplifies form creation and management. It provides a structured way to define form fields, validate inputs, and render forms efficiently.

## Features
- Modular form field definitions
- Custom field validation
- Extendable form builder
- Default field implementations
- Easy-to-use API for seamless integration

## Installation
Ensure Composer is installed, then run:

```sh
composer install simp/forms
```

## Usage
### Example Form
The `Example.php` file demonstrates form creation:

```php
<?php

require_once "vendor/autoload.php";

use Simp\FormBuilder\FormBase;

class Example extends FormBase
{
    public function getFormId(): string
    {
        return "example_form";
    }

    public function buildForm(array &$form): array
    {
        $this->setFormAction('index1.php');

        $form[] = [
            'name' => 'first_name',
            'label' => 'First Name',
            'type' => 'text',
            'id' => 'first_name',
            'class' => ['form-control', 'form-control-sm'],
            'options' => ['placeholder' => 'Enter your first name'],
            'required' => true,
        ];

        $form[] = [
            'name' => 'gender',
            'label' => 'Gender',
            'type' => 'select',
            'id' => 'gender',
            'class' => ['form-control', 'form-control-sm'],
            'default_value' => 'male',
            'option_values' => ['male' => 'Male', 'female' => 'Female', 'other' => 'Other'],
            'handler' => \Simp\Default\SelectField::class,
        ];

        $form[] = [
            'name' => 'profile_image[]',
            'label' => 'Profile Image',
            'type' => 'file',
            'id' => 'profile_image',
            'class' => ['form-control', 'form-control-sm'],
            'options' => ['accept' => 'image/*', 'multiple' => 'multiple'],
            'handler' => \Simp\Default\FileField::class,
            'required' => true,
        ];

        $form[] = [
            'type' => 'submit',
            'name' => 'submit',
            'id' => 'submit',
            'class' => ['btn', 'btn-primary'],
            'default_value' => 'Submit',
        ];

        return $form;
    }

    public function validateForm(array $form): void
    {
        foreach ($form as $field) {
            $value = $field->getValue();
            if (empty($value) && !empty($field->getRequired())) {
                $field->setError($field->getLabel() . ' is required!');
            }
        }
    }

    public function submitForm(array &$form): void
    {
        print_r($form);
    }
}
```

### Rendering the Form
Use `index.php` to render the form:

```php
require_once "vendor/autoload.php";
require_once "Example.php";

try {
    $form_base = new \Simp\FormBuilder\FormBuilder(new Example());
    
    $form_base->getFormBase()->setFormMethod('POST');
    $form_base->getFormBase()->setFormEnctype('multipart/form-data');
    $form_base->getFormBase()->isFormSilent(true);
    $form_base->getFormBase()->setSilentHandler(['submit_handler']);
    $form_base->getFormBase()->setIsJsAllowed(true);
    $form_base->getFormBase()->setJsLibrary(['/main.js']);
    $form_base->getFormBase()->setFieldJsEvents('change', ['handle_on_change']);
    
    echo $form_base;
} catch (Exception $e) {
    echo "Error rendering form: " . $e->getMessage();
}
```

## Extending Form Fields
To create custom fields, extend the `FieldBase` class. Check the `Default` field implementations for reference.

## Contributing
Feel free to contribute by submitting issues or pull requests to improve this library.

## License
This project is licensed under the MIT License.


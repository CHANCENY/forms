# Form Library

## Overview
This is a PHP-based form library designed to simplify form creation and management. It provides a structured approach to handling form fields, validation, and form rendering.

## Features
- Modular form field definitions
- Custom field validation
- Extendable form builder
- Default field implementations
- Easy-to-use API for integrating with applications

## Installation
To use this library in your project, ensure you have Composer installed and require the necessary files:

```sh
composer install
```

Or manually include the required files:

```php
require_once 'src/FormBuilder/FormBuilder.php';
require_once 'src/Fields/FieldBase.php';
require_once 'Example.php';
```

## Usage
### Example Form
The `Example.php` file demonstrates how to define a form:

```php
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
        $form[] = [
            'name' => 'first_name',
            'label' => 'First Name',
            'type' => 'text',
            'id' => 'first_name',
            'class' => ['form-control', 'form-control-sm'],
        ];
        return $form;
    }
}
```

### Rendering the Form
The `index.php` file loads the example form and renders it:

```php
require_once "vendor/autoload.php";
require_once "Example.php";

try {
    $form_base = new \Simp\FormBuilder\FormBuilder(new Example());
    echo $form_base;
} catch (Exception $e) {
    echo "Error rendering form: " . $e->getMessage();
}
```
 
## Contributing
Feel free to contribute by submitting issues or pull requests to improve this library.

## License
This project is licensed under the MIT License.


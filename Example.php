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
        // You can modify form settings from here.
        $this->setFormAction('index1.php');

        // Define fields.
        $form[] = [
            'name' => 'first_name',
            'label' => 'First Name',
            'type' => 'text',
            'id' => 'first_name',
            'class' => [
                'form-control',
                'form-control-sm',
            ],
            'default_value' => '',
            'options' => [
                'placeholder' => 'Enter your first name',
            ],
            'required' => true,
        ];

        $form[] = [
            'name' => 'gender',
            'label' => 'Gender',
            'type' => 'select',
            'id' => 'gender',
            'class' => [
                'form-control',
                'form-control-sm',
            ],
            'default_value' => 'male',
            'option_values' => [
                'male' => 'Male',
                'female' => 'Female',
                'other' => 'Other',
            ],
            'handler' => \Simp\Default\SelectField::class,
        ];

        $form[] = [
            'name' => 'profile_image[]',
            'label' => 'Profile Image',
            'type' => 'file',
            'id' => 'profile_image',
            'class' => [
                'form-control',
                'form-control-sm',
            ],
            'default_value' => null,
            'options' => [
                'accept' => 'image/*',
                'multiple' => 'multiple',
            ],
            'handler' => \Simp\Default\FileField::class,
            'required' => true,
        ];

        $form[] = [
            'type' => 'submit',
            'name' => 'submit',
            'id' => 'submit',
            'class' => ['btn','btn-primary'],
            'default_value' => 'Submit',
            'label' => '',
        ];

        return $form;
    }

    public function validateForm(array $form): void
    {
        foreach ($form as $field) {
            $value = $field->getValue();
            if(empty($value) && !empty($field->getRequired())) {
                $field->setError($field->getLabel(). ' is required!');
            }
        }
    }

    public function submitForm(array &$form): void
    {
        print_r($form);
    }
}
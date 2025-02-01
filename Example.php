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
        $form[] = [
            'name' => 'first_name',
            'label' => 'First Name',
            'type' => 'text',
            'id' => 'first_name',
            'class' => [
                'form-control',
                'form-control-sm',
            ],
            'default_value' => 'John',
            'options' => [
                'placeholder' => 'Enter your first name',
            ],
            'required' => true,
        ];
        $form[] = [
            'name' => 'last_name',
            'label' => 'Last Name',
            'type' => 'text',
            'id' => 'last_name',
            'class' => [
                'form-control',
                'form-control-sm',
            ],
            'default_value' => 'John',
            'options' => [
                'placeholder' => 'Enter your Last name',
            ]
        ];
        $form[] = [
            'name' => 'age',
            'label' => 'Age',
            'type' => 'number',
            'id' => 'age',
            'class' => [
                'form-control',
                'form-control-sm',
            ],
            'options' => [
                'placeholder' => 'Enter your age',
                'max' => 50,
                'min' => 10,
            ]
        ];
        $form[] = [
            'name' => 'birthday',
            'label' => 'Birthday',
            'type' => 'date',
            'id' => 'birthday',
            'class' => [
                'form-control',
                'form-control-sm',
            ],
            'default_value' => '2010-01-01',
        ];
        $form[] = [
            'name' => 'gender[]',
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
                'transgender' => 'Transgender',
                'other' => 'Others',
            ],
            'description' => 'Select your gender',
            'required' => true,
            'options' => [
                'multiple' => 'multiple',
            ],
            'handler' => \Simp\Default\SelectField::class
        ];
        $form[] = [
            'name' => 'height',
            'label' => 'Height',
            'type' => 'range',
            'id' => 'height',
            'options' => [
                'min' => 1,
                'max' => 10,
            ],
            'default_value' => '6',
            'class' => [
                'form-control',
                'form-control-sm',
            ],
            'description' => 'Height is in feet'
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
        dump($form);
    }
}
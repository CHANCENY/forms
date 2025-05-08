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

        $form['profile_image'] = [
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

        // Create fieldset field.
        $form['field_set_work'] = [
            'label' => 'Work Experience Section',
            'type' => 'fieldset',
            'id' => 'field_set_work',
            'class' => ['form-control'],
            'name' => 'field_set_work',

            // Adding inner field of fieldset.
            'inner_field' => [
                'total_year_worked' => [
                    'label' => 'Years of Work',
                    'type' => 'number',
                    'name' => 'year_worked',
                    'id' => 'year_worked',
                    'class' => ['form-control'],
                    'required' => true,
                    'default_value' => '',
                ],
                'one_company_worked_for' => [
                    'label' => 'One Company Work In last Year',
                    'type' => 'text',
                    'name' => 'one_company_worked_for',
                    'id' => 'one_company_worked_for',
                    'class' => ['form-control'],
                    'default_value' => '',
                    'required' => true,
                ],

                // Inner field can also be fieldset
                "company_information" => [
                    'label' => 'Company Information',
                    'type' => 'fieldset',
                    'id' => 'company_information',
                    'class' => ['form-control'],
                    'name' => 'company_information',
                    // Inner fields of this field.
                    'inner_field' => [
                        'company_title' => [
                            'label' => 'Company Title',
                            'type' => 'text',
                            'name' => 'company_title',
                            'id' => 'company_title',
                            'class' => ['form-control'],
                            'required' => true,
                            'default_value' => '',
                        ],
                        'company_address' => [
                            'label' => 'Company Address',
                            'type' => 'text',
                            'name' => 'company_address',
                            'id' => 'company_address',
                            'class' => ['form-control'],
                            'required' => true,
                            'default_value' => '',
                        ],
                        // Creating Detail wrapper of given fields.
                        'current_working' => [
                            'label' => 'Please specify the current job you are doing on company mentioned above',
                            'type' => 'details',
                            'id' => 'current_working',
                            'class' => ['form-control'],
                            'name' => 'current_working',
                            'options' => [
                                'closed' => "closed",
                            ],
                            // Fields to be inside Details wrapper
                            'inner_field' => [
                                'job_title' => [
                                    'label' => 'Job Title',
                                    'type' => 'text',
                                    'name' => 'job_title',
                                    'id' => 'job_title',
                                    'class' => ['form-control'],
                                    'required' => true,
                                    'default_value' => '',
                                ],
                                'job_salary' => [
                                    'label' => 'Job Salary',
                                    'type' => 'text',
                                    'name' => 'job_salary',
                                    'id' => 'job_salary',
                                    'class' => ['form-control'],
                                    'required' => true,
                                    'default_value' => '',
                                ]
                            ],
                            'handler' => \Simp\Default\DetailWrapperField::class,
                        ]
                    ],
                    'handler' => \Simp\Default\FieldSetField::class,
                ]
            ],
            'handler' => \Simp\Default\FieldSetField::class,
        ];

        // You can make conditional field also
        $form['conditional_field'] = [
            'label' => 'Do you have any college or university information you would like to share?',
            'type' => 'conditional',
            'id' => 'conditional_field',
            'class' => ['form-control'],
            'name' => 'conditional_field',
            'inner_field' => [
                'college_name' => [
                    'label' => 'College Name',
                    'type' => 'text',
                    'name' => 'college_name',
                    'id' => 'college_name',
                    'class' => ['form-control'],
                ],
                'college_certificate' => [
                    'label' => 'College Certificate',
                    'type' => 'file',
                    'id' => 'college_certificate',
                    'class' => ['form-control'],
                    'name' => 'college_certificate',
                    'handler' => \Simp\Default\FileField::class,
                ],
                'college_address_available' => [
                    'label' => 'Do you remember your college address?',
                    'type' => 'checkbox',
                    'name' => 'college_address_available',
                    'id' => 'college_address_available',
                    'class' => ['form-control'],
                ],
                'college_address' => [
                    'label' => 'College Address',
                    'type' => 'text',
                    'name' => 'college_address',
                    'id' => 'college_address',
                    'class' => ['form-control'],
                ]

            ],
            'handler' => \Simp\Default\ConditionalField::class,
            'conditions' => [
                'college_name' => [
                    'event' => 'input',
                    'receiver_field' => 'college_certificate'
                ],
                'college_address_available' => [
                    'event' => 'input',
                    'receiver_field' => 'college_address'
                ]
            ]
        ];

        $form['description_field'] = [
            'label' => 'what would you like us to know about you?',
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'class' => ['form-control'],
            'required' => true,
            'sanitize' => true,
            'handler' => \Simp\Default\TextAreaField::class,
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
    }

    public function submitForm(array &$form): void
    {
        //dump($form);
    }
}
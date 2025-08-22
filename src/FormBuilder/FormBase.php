<?php

namespace Simp\FormBuilder;


abstract class FormBase implements FormInterface {

    protected mixed $options;

    protected array $form_fields = [];
    
    private array $form_settings = [
        'method' => 'post',
        'action' => '',
        'enctype' => 'multipart/form-data',
        'accept-charset' => 'utf-8',
        'validate' => true,
        'is_js_allow' => false,
        'js_settings' => [
            'silent_submission' => false,
            'silent_submit_handler' => []
        ],
        'js_field_custom_function' => [

        ],
        'js_library' => []
    ];

    public function __construct(mixed $options = [])
    {
        $this->options = $options;
    }

    public function setFormMethod(string $method): void
    {
        $this->form_settings['method'] = $method;
    }
    public function setFormAction(string $action): void
    {
        $this->form_settings['action'] = $action;
    }
    public function setFormEnctype(string $enctype): void
    {
        $this->form_settings['enctype'] = $enctype;
    }
    public function setFormAcceptCharset(string $charset): void
    {
        $this->form_settings['accept-charset'] = $charset;
    }
    public function validation(bool $validation = TRUE): void
    {
        $this->form_settings['validate'] = $validation;
    }
    public function isFormSilent(bool $is_silent = false): void
    {
        $this->form_settings['js_settings']['silent_submission'] = $is_silent;
    }
    public function setSilentHandler(array $js_functions_handlers): void
    {
        $this->form_settings['js_settings']['silent_submit_handler'] = array_merge($this->form_settings['js_settings']['silent_submit_handler'], $js_functions_handlers);
    }
    public function setFieldJsEvents(string $js_event, array $event_js_handlers): void
    {
        $this->form_settings['js_field_custom_function'][$js_event] =

            !empty($this->form_settings['js_field_custom_function'][$js_event]) ?

            array_merge($this->form_settings['js_field_custom_function'][$js_event], $event_js_handlers)
                : $event_js_handlers;
    }

    public function setJsLibrary(array $library): void
    {
        $this->form_settings['js_library'] = array_merge($this->form_settings['js_library'], $library);
    }
    public function getIsSilent(): bool
    {
        return $this->form_settings['js_settings']['silent_submission'];
    }
    public function getSilentSubmissionHandler(): array
    {
        return $this->form_settings['js_settings']['silent_submit_handler'];
    }
    public function getFieldJsEvents(): array
    {
        return $this->form_settings['js_field_custom_function'];
    }
    public function getIsValidation(): string
    {
        return empty($this->form_settings['validate']) ? 'novalidate' : 'validate';
    }
    public function getJsLibrary()
    {
        return $this->form_settings['js_library'];
    }
    public function getAcceptCharset()
    {
        return $this->form_settings['accept-charset'];
    }
    public function getAction(): string
    {
        return $this->form_settings['action'];
    }
    public function getFormEnctype(): string
    {
        return $this->form_settings['enctype'];
    }
    public function getFormMethod(): string
    {
        return $this->form_settings['method'];
    }

    public function getIsJsAllowed()
    {
        return $this->form_settings['is_js_allow'];
    }

    public function setIsJsAllowed(bool $is_js_allow): void
    {
        $this->form_settings['is_js_allow'] = $is_js_allow;
    }

    public function buildForm(array $form): array
    {
        return $form;
    }

    public function setFormFields(array $form_fields): void
    {
        $this->form_fields = $form_fields;
    }

    public function getFormFields(): array {
        return $this->form_fields;
    }
    
}
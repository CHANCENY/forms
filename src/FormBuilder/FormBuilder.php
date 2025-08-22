<?php

namespace Simp\FormBuilder;

use Simp\Default\BasicField;

class FormBuilder
{
    public string $form;
    private FormBase $formBase;

    private array $fields = [];


    public function __construct(FormBase $formBase)
    {
        $this->formBase = $formBase;
        $files = $_FILES;
        $post = $_POST;
        $params = $_GET;
        $method = $_SERVER['REQUEST_METHOD'];

        $form = $formBase->buildForm($formBase->getFormFields());
        
        foreach ($form as $key=>$field) {
            $handler = $field['handler'] ?? BasicField::class;
            $object = new $handler($field, $method, $post, $params, $files);
            $form[$key] = $object;
            $this->fields[$key] = $object;
        }

        if ($method === 'POST') {

            $formBase->validateForm($form);
            $formBase->submitForm($form);

        }


    }

    public function getForm(): string {

        $this->form = <<<FORM
<form action="{$this->formBase->getAction()}" method="{$this->formBase->getFormMethod()}" novalidate="{$this->formBase->getIsValidation()}" 
enctype="{$this->formBase->getFormEnctype()}" accept-charset="{$this->formBase->getAcceptCharset()}"
 id="{$this->formBase->getFormId()}" class="form">
FORM;

        $form_id = $this->formBase->getFormId();

        foreach ($this->fields as $field) {
            $this->form .= $field;
        }

        $this->form .= "</form>";

        // Add library
        if($this->formBase->getJsLibrary()) {
            $all = '';
            foreach ($this->formBase->getJsLibrary() as $js) {
                if(str_ends_with($js, '</script>')) {
                    $all .= $js;
                }else{
                    $all .= "<script type=\"text/javascript\" src=\"{$js}\"></script>";
                }
            }
            $this->form .= $all.PHP_EOL;
        }

        // Js
        $js = "<script type=\"text/javascript\">";
        if ($this->formBase->getIsJsAllowed()) {

            $handlers = $this->formBase->getSilentSubmissionHandler();
            foreach ($handlers as $k=>$handler) {
                $handlers[$k] = $handler. "(e,form)";
            }
            $handlers = implode("\n", $handlers);


            $submission = '';
            $field_js = '';
            if($this->formBase->getIsSilent()) {
                $submission = <<<SUBMISSION
const form = document.querySelector('#{$form_id}');
        form.addEventListener('submit',(e)=>{
            {$handlers}
        });
SUBMISSION;
            }

            if($this->formBase->getFieldJsEvents()) {
                $functions = $this->formBase->getFieldJsEvents();
                $events = '';
                foreach ($functions as $k=>$function) {
                    $functions[$k] = array_map(function ($handler) {
                        return $handler. "(e, field)";
                    },$function);
                }
                $function_all = '';
                foreach ($functions as $function) {
                    $function_all .= implode("\n", $function);
                }

                foreach ($functions as $k=>$function) {
                    $events .= <<<EVENTS
 field.addEventListener('$k',(e)=>{
 {$function_all}
 })
EVENTS;
                }
                $field_js = <<<FIELD_JS
const form_l = document.querySelector('#{$form_id}');
if (form_l) {
   
   const fields = form_l.querySelectorAll('input, select, textarea');
   fields.forEach((field) =>{
        {$events}
   })

}
FIELD_JS;
            }
            $js .= <<<JS
(function() {
    document.addEventListener('DOMContentLoaded',(e)=>{
        {$submission}
        {$field_js}
    });
})();
JS;
        }
        $js .= "</script>";
        $this->form .= $js;
        return $this->form;
    }

    public function __toString(): string
    {

        return $this->getForm();
    }

    public function getFormBase(): FormBase
    {
        return $this->formBase;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function __toOnlyFieldString()
    {
        $form = '';
        foreach ($this->fields as $field) {
            $form .= $field;
        }
        return $form;
    }

}
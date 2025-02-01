<?php

namespace Simp\FormBuilder;

use Simp\Default\BasicField;

class FormBuilder
{
    public string $form;

    /**
     * @throws \Exception
     */
    public function __construct(FormBase $formBase)
    {
        if(session_status() == PHP_SESSION_DISABLED) {
            throw new \RuntimeException('Sessions are disabled');
        }

        $files = $_FILES;
        $post = $_POST;
        $params = $_GET;
        $method = $_SERVER['REQUEST_METHOD'];
        $form_id = $formBase->getFormId();

        $this->form = "<form method='post' id='{$form_id}' class='form form-{$form_id} js-form--$form_id' enctype='multipart/form-data'>";

        $form = [];
        $form = $formBase->buildForm($form);
        // Build fields
        foreach ($form as $key=>$field) {
            $handler = $field['handler'] ?? BasicField::class;
            $object = new $handler($field, $method, $post, $params, $files);
            $form[$key] = $object;
        }

        if ($method === 'POST') {

            $formBase->validateForm($form);
            $formBase->submitForm($form);

        }

        foreach ($form as $field) {

            $this->form .= $field->__toString();

        }
        $this->form .= "</form>";
    }

    public function getForm(): string {
        return $this->form;
    }

    public function __toString(): string
    {
        return $this->form;
    }

}
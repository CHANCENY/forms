<?php

namespace Simp\FormBuilder;

interface FormInterface {

    public function getFormId(): string;

    public function buildForm(array $form): array;

    public function validateForm(array $form): void;

    public function submitForm(array $form): void;

}
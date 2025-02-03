<?php

require_once "vendor/autoload.php";
require_once "Example.php";

$form_base = null;

try {
    $form_base = new \Simp\FormBuilder\FormBuilder(new Example());

    // You can get form object to override form settings.
    $form_base->getFormBase()->setFormMethod('POST');
    $form_base->getFormBase()->setFormEnctype('multipart/form-data');
    $form_base->getFormBase()->isFormSilent(true);
    $form_base->getFormBase()->setSilentHandler(['submit_handler']);
    $form_base->getFormBase()->setIsJsAllowed(true);
    $form_base->getFormBase()->setJsLibrary(['/main.js']);
    $form_base->getFormBase()->setFieldJsEvents('change', ['handle_on_change']);

} catch (Exception $e) {

}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
<?php

echo $form_base;

?>

</body>
</html>

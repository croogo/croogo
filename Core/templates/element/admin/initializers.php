<?php

$adminThemeScripts = <<<EOF
    Admin.form();
    Admin.protectForms();
    Admin.formFeedback();
    Admin.extra();
    Admin.slideBoxToggle();
    Admin.dateTimeFields();
    Admin.navigation();
    Admin.modal();

EOF;

if (!$this->getRequest()->is('ajax')) :
    $this->Js->buffer($adminThemeScripts);
endif;

<?php

$adminThemeScripts = <<<EOF
    Admin.form();
    Admin.protectForms();
    Admin.formFeedback();
    Admin.extra();
    Admin.slideBoxToggle();
    Admin.dateTimeFields();
EOF;

$this->Js->buffer($adminThemeScripts);

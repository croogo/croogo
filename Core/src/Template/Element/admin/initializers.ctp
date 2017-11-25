<?php

$adminThemeScripts = <<<EOF
    Admin.form();
    Admin.protectForms();
    Admin.formFeedback();
    Admin.extra();
    Admin.slideBoxToggle();
    Admin.dateTimeFields();
EOF;

$this->Html->scriptBlock($adminThemeScripts, ['block' => 'scriptBottom']);

<?php

$adminThemeScripts = <<<EOF
	Admin.form();
	Admin.protectForms();
	Admin.extra();
	Admin.slideBoxToggle();
	Admin.dateTimeFields();
EOF;

$this->Html->scriptBlock($adminThemeScripts, ['block' => 'scriptBottom']);

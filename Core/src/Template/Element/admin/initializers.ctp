<?php

$adminThemeScripts =<<<EOF
	Admin.form();
	Admin.protectForms();
	Admin.extra();
	Admin.slideBoxToggle();
EOF;

$this->Html->scriptBlock($adminThemeScripts, ['block' => 'scriptBottom']);

<?php

$adminThemeScripts =<<<EOF
	Admin.form();
	Admin.protectForms();
	Admin.extra();
	Admin.slideBoxToggle();
EOF;
$this->Js->buffer($adminThemeScripts);

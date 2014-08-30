<?php

$this->extend('/Common/admin_index');
$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Users'), '/' . $this->request->url);
?>

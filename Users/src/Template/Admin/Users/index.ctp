<?php

$this->extend('Croogo/Croogo./Common/admin_index');
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Users'), '/' . $this->request->url);
?>

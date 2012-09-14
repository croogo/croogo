<?php
$this->extend('Croogo./Common/admin_index');
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Users'), $this->here);
?>

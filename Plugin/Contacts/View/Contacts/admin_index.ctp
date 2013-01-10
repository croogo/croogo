<?php
$this->Html->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Contacts'), $this->here);

$this->extend('Croogo./Common/admin_index');
?>

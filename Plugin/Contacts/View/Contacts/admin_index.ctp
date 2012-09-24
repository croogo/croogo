<?php
$this->Html->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Contacts'), $this->here);

$this->extend('/Common/admin_index');
?>

<?php
$this->Html->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Contacts'), $this->here);

$this->extend('/Common/admin_index');
?>

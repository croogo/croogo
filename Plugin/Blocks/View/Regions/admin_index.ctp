<?php
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Blocks'), array('plugin' => 'blocks', 'controller' => 'blocks', 'action' => 'index'))
	->addCrumb(__('Regions'), $this->here);

$this->extend('/Common/admin_index');
?>

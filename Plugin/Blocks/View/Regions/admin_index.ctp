<?php
$this->extend('Croogo./Common/admin_index');
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Blocks'), array('plugin' => 'blocks', 'controller' => 'blocks', 'action' => 'index'))
	->addCrumb(__('Regions'), $this->here);
?>

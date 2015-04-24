<?php
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Types'), '/' . $this->request->url);

echo $this->extend('Croogo/Croogo./Common/admin_index');
?>

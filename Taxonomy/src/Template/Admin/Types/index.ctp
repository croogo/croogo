<?php
$this->Html
		->addCrumb(__d('croogo', 'Content'), array('plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Types'), '/' . $this->request->url);

echo $this->extend('Croogo/Core./Common/admin_index');
?>

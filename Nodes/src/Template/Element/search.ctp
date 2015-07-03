<?php
	echo $this->Form->create(false, array('url' => array('prefix' => false, 'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'search')));
	$this->Form->unlockField('q');
	echo $this->Form->input('q', array(
		'label' => false,
	));
	echo $this->Form->button(__d('croogo', 'Search'));
	echo $this->Form->end();
?>

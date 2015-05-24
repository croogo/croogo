<?php
	echo $this->Form->create(null, array('admin' => false, 'plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'search'));
	$this->Form->unlockField('q');
	echo $this->Form->input('q', array(
		'label' => false,
	));
	echo $this->Form->button(__d('croogo', 'Search'));
	echo $this->Form->end();
?>
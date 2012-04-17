<?php
$this->extend('/Common/admin_edit');
$this->set('className', 'acl_actions');
?>
<?php echo $this->Form->create('Aco', array('url' => array('controller' => 'acl_actions', 'action' => 'edit'))); ?>
	<fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('parent_id', array(
			'options' => $acos,
			'empty' => true,
			'rel' => __('Choose none if the Aco is a controller.'),
		));
		echo $this->Form->input('alias', array());
	?>
	</fieldset>
<?php echo $this->Form->end('Submit'); ?>
<div class="nodes filter form">
<?php
	echo $this->Form->create('Node', array(
		'url' => array_merge(array('action' => 'index'), $this->params['pass'])
	));
	echo $this->Form->input('type', array(
		'empty' => true,
		'options' => $nodeTypes
	));
	echo $this->Form->input('status', array(
		'empty' => true,
		'options' => array(
			'1' => __('Published'),
			'0' => __('Unpublished'),
		),
	));
	echo $this->Form->input('promote', array(
		'label' => __('Promoted'),
		'empty' => true,
		'options' => array(
			'1' => __('Yes'),
			'0' => __('No'),
		),
	));
	echo $this->Form->input('filter', array('label' => __('Title')));
	echo $this->Form->end(__('Filter'));
?>
<div class="clear">&nbsp;</div>
</div>

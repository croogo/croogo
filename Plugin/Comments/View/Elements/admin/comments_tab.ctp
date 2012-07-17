<?php
echo $this->Form->input('comment_status', array(
	'type' => 'radio',
	'div' => array('class' => 'radio'),
	'options' => array(
		'0' => __('Disabled'),
		'1' => __('Read only'),
		'2' => __('Read/Write'),
	),
	'value' => $type['Type']['comment_status'],
));
?>

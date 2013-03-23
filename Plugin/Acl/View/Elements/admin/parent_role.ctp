<?php
echo $this->Form->input('parent_id', array(
	'empty' => true,
	'rel' => __d('croogo', 'When set, permissions from parent role are inherited'),
	'class' => '',
));

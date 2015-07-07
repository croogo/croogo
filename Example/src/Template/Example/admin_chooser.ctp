<?php

$this->extend('/Common/admin_index');
$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb('Example', array('controller' => 'example', 'action' => 'index'))
	->addCrumb('Chooser Example', array('controller' => 'example', 'action' => 'chooser'));

$this->append('form-start', $this->Form->create(null));

$this->append('main');
echo $this->Form->input('node_id', array(
	'type' => 'text',
	'append' => true,
	'addon' => $this->Html->link('Choose Node',
		array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'?' => array(
				'chooser' => true,
				'KeepThis' => true,
				'TB_iframe' => true,
			),
		),
		array(
			'button' => 'default',
			'class' => 'action node chooser',
		)
	)
));

echo $this->Form->input('node_url', array(
	'type' => 'text',
	'append' => true,
	'addon' => $this->Html->link('Choose Node',
		array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'?' => array(
				'chooser' => true,
				'KeepThis' => true,
				'TB_iframe' => true,
			),
		),
		array(
			'button' => 'default',
			'class' => 'action node chooser',
		)
	)
));

echo $this->Form->input('block_id', array(
	'type' => 'text',
	'append' => true,
	'addon' => $this->Html->link('Choose Block Id',
		array(
			'plugin' => 'blocks',
			'controller' => 'blocks',
			'action' => 'index',
			'?' => array(
				'chooser' => true,
				'KeepThis' => true,
				'TB_iframe' => true,
			),
		),
		array(
			'button' => 'default',
			'class' => 'action block chooser',
		)
	)
));

echo $this->Form->input('block_title', array(
	'type' => 'text',
	'append' => true,
	'addon' => $this->Html->link('Choose Block Title',
		array(
			'plugin' => 'blocks',
			'controller' => 'blocks',
			'action' => 'index',
			'?' => array(
				'chooser' => true,
				'KeepThis' => true,
				'TB_iframe' => true,
			),
		),
		array(
			'button' => 'default',
			'class' => 'action block chooser',
		)
	)
));

echo $this->Form->input('user_id', array(
	'type' => 'text',
	'append' => true,
	'addon' => $this->Html->link('Choose User Id',
		array(
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'index',
			'?' => array(
				'chooser' => true,
				'KeepThis' => true,
				'TB_iframe' => true,
			),
		),
		array(
			'button' => 'default',
			'class' => 'action user chooser',
		)
	)
));
$this->end();

$this->append('form-end', $this->Form->end());

$script = <<<EOF
$('.chooser').itemChooser({
	fields: [
		{ type: "Node", target: '#SettingNodeId', attr: 'data-chooser_id' },
		{ type: "Node", target: '#SettingNodeUrl', attr: 'rel' },
		{ type: "Block", target: '#SettingBlockId', attr: 'data-chooser_id' },
		{ type: "Block", target: '#SettingBlockTitle', attr: 'data-chooser_title' },
		{ type: "User", target: '#SettingUserId', attr: 'data-chooser_id' }
	]
});
EOF;
$this->Js->buffer($script);

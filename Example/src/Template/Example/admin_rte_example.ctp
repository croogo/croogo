<?php

$this->extend('/Common/admin_index');
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb('Example', array('controller' => 'example', 'action' => 'index'))
	->addCrumb('RTE Example', '/' . $this->request->url);

echo $this->Form->create('Example');

$options = array('type' => 'textarea');
$rteConfigs = Configure::read('Wysiwyg.actions.Example/admin_rte_example');

$para = '<p>This editor was configured with the following setting:</p>';
foreach (array('basic', 'standard', 'full', 'custom') as $preset):
	$query = sprintf('{n}[elements=Example%s]', Inflector::camelize($preset));
	$presetConfig = Hash::extract($rteConfigs, $query);
	$pre = '<blockquote><pre>' . print_r($presetConfig[0], true) . '</pre></blockquote>';
	echo $this->Form->input($preset, Hash::merge(array(
		'value' => $para . $pre,
	), $options));
endforeach;

echo $this->Form->end();

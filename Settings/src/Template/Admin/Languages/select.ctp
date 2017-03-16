<?php

$this->extend('/Common/admin_index');

$this->Breadcrumbs
	->add(__d('croogo', 'Settings'), array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'index'))
	->add(__d('croogo', 'Languages'), array('plugin' => 'settings', 'controller' => 'languages', 'action' => 'index'));

$this->append('action-buttons');
	echo $this->Html->link(
		__d('croogo', 'New Language'),
		array('action' => 'add'),
		array('button' => 'default')
	);
$this->end();

$this->append('main');
	$html = null;
	foreach ($languages as $language):
		$title = $language['Language']['title'] . ' (' . $language['Language']['native'] . ')';
		$link = $this->Html->link($title, array(
			'plugin' => 'translate',
			'controller' => 'translate',
			'action' => 'edit',
			$id,
			$modelAlias,
			'locale' => $language['Language']['alias'],
		));
		$html .= '<li>' . $link . '</li>';
	endforeach;
	echo $this->Html->div(
		$this->Theme->getCssClass('columnFull'),
		$this->Html->tag('ul', $html)
	);
$this->end();

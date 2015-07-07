<?php
$this->extend('/Common/admin_edit');
$this->set('className', 'translate');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(Inflector::humanize(Inflector::pluralize($modelAlias)))
	->addCrumb($this->data[$modelAlias][$displayField])
	->addCrumb(
		__d('croogo', 'Translations'),
		array(
			'plugin' => 'translate',
			'controller' => 'translate',
			'action' => 'index',
			$id,
			$modelAlias,
		)
	)
	->addCrumb(__d('croogo', 'Translate'), '/' . $this->request->url);

$this->append('form-start', $this->Form->create($modelAlias, array(
	'url' => array(
		'plugin' => 'translate',
		'controller' => 'translate',
		'action' => 'edit',
		$id,
		$modelAlias,
		'locale' => $this->request->params['named']['locale'],
	)
)));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Translate'), '#translate-main');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('translate-main');
		foreach ($fields as $field):
			echo $this->Form->input($modelAlias . '.' . $field);
		endforeach;
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
		$this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
		$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index', $this->request->params['pass'][0], $this->request->params['pass'][1]), array(
			'class' => 'cancel',
			'button' => 'danger'
		));
	echo $this->Html->endBox();
$this->end();

$this->append('form-end', $this->Form->end());

<?php

$this->Croogo->adminScript('Contacts.admin');

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Contacts'), array('controller' => 'contacts', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Messages'), array('action' => 'index'));

if (isset($criteria['Message.status'])) {
	if ($criteria['Message.status'] == '1') {
		$this->Html->addCrumb(__d('croogo', 'Read'), '/' . $this->request->url);
		$this->viewVars['title_for_layout'] = __d('croogo', 'Messages: Read');
	} else {
		$this->Html->addCrumb(__d('croogo', 'Unread'), '/' . $this->request->url);
		$this->viewVars['title_for_layout'] = __d('croogo', 'Messages: Unread');
	}
}

$script = <<<EOF
$(".comment-view").on("click", function() {
	var el= \$(this)
	var modal = \$('#comment-modal');
	$('#comment-modal')
		.find('.modal-header h3').html(el.data("title")).end()
		.find('.modal-body').html('<pre>' + el.data('content') + '</pre>').end()
		.modal('toggle');
});
EOF;
$this->Js->buffer($script);

$this->append('table-footer', $this->element('admin/modal', array(
	'id' => 'comment-modal',
	)
));


$this->append('actions');
	echo $this->Croogo->adminAction(__d('croogo', 'Unread'), array(
		'action' => 'index',
		'?' => array(
			'status' => '0',
		),
	));
	echo $this->Croogo->adminAction(__d('croogo', 'Read'), array(
		'action' => 'index',
		'?' => array(
			'status' => '1',
		),
	));
$this->end();

$this->append('form-start', $this->Form->create('Message', array('url' => array('controller' => 'messages', 'action' => 'process', 'class' => 'form-inline'))));

$this->start('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Form->checkbox('checkAll'),
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('contact_id', __d('croogo', 'Contact')),
		$this->Paginator->sort('name', __d('croogo', 'Name')),
		$this->Paginator->sort('email', __d('croogo', 'Email')),
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		__d('croogo', 'Actions'),
	));
	echo $this->Html->tag('thead',$tableHeaders);
$this->end();

$this->append('table-body');
	$commentIcon = $this->Html->icon($_icons['comment']);
	$rows = array();
	foreach ($messages as $message) {
		$actions = array();

		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $message['Message']['id']),
			array('icon' => $_icons['update'], 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Message' . $message['Message']['id'] . 'Id',
			array(
				'icon' => $_icons['delete'],
				'class' => 'delete',
				'tooltip' => __d('croogo', 'Remove this item'),
				'rowAction' => 'delete',
			),
			__d('croogo', 'Are you sure?')
		);
		$actions[] = $this->Croogo->adminRowActions($message['Message']['id']);

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$rows[] = array(
			$this->Form->checkbox('Message.' . $message['Message']['id'] . '.id', array('class' => 'row-select')),
			$message['Message']['id'],
			$message['Contact']['title'],
			$message['Message']['name'],
			$message['Message']['email'],
			$commentIcon . ' ' .
			$this->Html->link($message['Message']['title'], '#',
				array(
					'class' => 'comment-view',
					'data-target' => '#comment-modal',
					'data-title' => $message['Message']['title'],
					'data-content' => $message['Message']['body'],
				)
			),
			$actions,
		);
	}
	echo $this->Html->tableCells($rows);
$this->end();

$this->start('bulk-action');
	echo $this->Form->input('Message.action', array(
		'label' => false,
		'div' => 'input inline',
		'options' => array(
			'read' => __d('croogo', 'Mark as read'),
			'unread' => __d('croogo', 'Mark as unread'),
			'delete' => __d('croogo', 'Delete'),
		),
		'empty' => true,
	));
	$button = $this->Form->button(__d('croogo', 'Submit'), array(
		'type' => 'submit',
		'value' => 'submit'
	));
	echo $this->Html->div('controls', $button);
$this->end();
$this->append('form-end', $this->Form->end());

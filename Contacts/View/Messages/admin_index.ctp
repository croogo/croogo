<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
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

echo $this->element('admin/modal', array(
	'id' => 'comment-modal',
	'class' => 'hide',
	)
);
?>

<?php $this->start('actions'); ?>
<?php
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
?>
<?php $this->end(); ?>

<?php

echo $this->Form->create('Message', array('url' => array('controller' => 'messages', 'action' => 'process')));

?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('contact_id', __d('croogo', 'Contact')),
		$this->Paginator->sort('name', __d('croogo', 'Name')),
		$this->Paginator->sort('email', __d('croogo', 'Email')),
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		__d('croogo', 'Actions'),
	));
?>
	<thead>
	<?php echo $tableHeaders; ?>
	</thead>

<?php
	$commentIcon = $this->Html->icon('comment-alt');
	$rows = array();
	foreach ($messages as $message) {
		$actions = array();

		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $message['Message']['id']),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Message' . $message['Message']['id'] . 'Id',
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item'), 'rowAction' => 'delete'),
			__d('croogo', 'Are you sure?')
		);
		$actions[] = $this->Croogo->adminRowActions($message['Message']['id']);

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$rows[] = array(
			$this->Form->checkbox('Message.' . $message['Message']['id'] . '.id'),
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
?>

</table>
<div class="row-fluid">
	<div id="bulk-action" class="control-group">
		<?php
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
		?>
		<div class="controls">
			<?php echo $this->Form->end(__d('croogo', 'Submit')); ?>
		</div>
	</div>
</div>

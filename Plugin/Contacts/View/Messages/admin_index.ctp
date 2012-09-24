<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Contacts'), array('controller' => 'contacts', 'action' => 'index'))
	->addCrumb(__('Messages'), array('action' => 'index'));

$script =<<<EOF
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

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link(__('Unread'), array('action'=>'index', 'filter' => 'status:0;'), array('button' => 'default')); ?></li>
<li><?php echo $this->Html->link(__('Read'), array('action'=>'index', 'filter' => 'status:1;'), array('button' => 'default')); ?></li>
<?php $this->end(); ?>

<?php
if (isset($this->params['named'])) {
	foreach ($this->params['named'] as $nn => $nv) {
		$this->Paginator->options['url'][] = $nn . ':' . $nv;
	}
}

echo $this->Form->create('Message', array('url' => array('controller' => 'messages', 'action' => 'process')));

?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		$this->Paginator->sort('id'),
		$this->Paginator->sort('contact_id'),
		$this->Paginator->sort('name'),
		$this->Paginator->sort('email'),
		$this->Paginator->sort('title'),
		__('Actions'),
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
			array('icon' => 'pencil', 'tooltip' => __('Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Message' . $message['Message']['id'] . 'Id',
			array('icon' => 'trash', 'tooltip' => __('Remove this item')),
			__('Are you sure?')
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
	<div class="span3">
		<?php
			echo $this->Form->input('Message.action', array(
				'label' => false,
				'class' => 'span11',
				'options' => array(
					'read' => __('Mark as read'),
					'unread' => __('Mark as unread'),
					'delete' => __('Delete'),
				),
				'empty' => true,
			));
		?>
	</div>
	<div class="span3">
		<?php echo $this->Form->end(__('Submit')); ?>
	</div>
</div>

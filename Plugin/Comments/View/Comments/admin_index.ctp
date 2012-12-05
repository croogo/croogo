<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__('Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__('Comments'), array('plugin' => 'comments', 'controller' => 'comments', 'action' => 'index'));

if (isset($criteria['Comment.status'])) {
	if ($criteria['Comment.status'] == '1') {
		$this->Html->addCrumb(__('Published'), $this->here);
		$this->viewVars['title_for_layout'] = __('Comments: Published');
	} else {
		$this->Html->addCrumb(__('Approval'), $this->here);
		$this->viewVars['title_for_layout'] = __('Comments: Approval');
	}
}

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
));

?>
<?php $this->start('actions'); ?>
	<li>
	<?php
		echo $this->Html->link(
			__('Published'),
			array('action'=>'index', 'status' => '1'),
			array('button' => 'default')
		);
	?>
	</li>
	<li>
	<?php
		echo $this->Html->link(
			__('Approval'),
			array('action'=>'index', 'status' => '0'),
			array('button' => 'default')
		);
	?>
	</li>
<?php $this->end(); ?>

<?php echo $this->Form->create('Comment', array('url' => array('controller' => 'comments', 'action' => 'process'))); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		$this->Paginator->sort('id'),
		//$this->Paginator->sort('title'),
		$this->Paginator->sort('name'),
		$this->Paginator->sort('email'),
		$this->Paginator->sort('node_id'),
		'',
		$this->Paginator->sort('created'),
		__('Actions'),
	));
?>
	<thead>
	<?php echo $tableHeaders; ?>
	</thead>
<?php

	$rows = array();
	foreach ($comments as $comment) {
		$actions = array();
		$actions[] = $this->Croogo->adminRowActions($comment['Comment']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $comment['Comment']['id']),
			array('icon' => 'pencil', 'tooltip' => __('Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Comment' . $comment['Comment']['id'] . 'Id',
			array('icon' => 'trash', 'tooltip' => __('Remove this item'), 'rowAction' => 'delete'),
			__('Are you sure?')
		);

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$title = empty($comment['Comment']['title']) ? 'Comment' : $comment['Comment']['title'];
		$rows[] = array(
			$this->Form->checkbox('Comment.' . $comment['Comment']['id'] . '.id'),
			$comment['Comment']['id'],
			$comment['Comment']['name'],
			$comment['Comment']['email'],
			$this->Html->link($comment['Node']['title'], array(
				'admin' => false,
				'plugin' => 'nodes',
				'controller' => 'nodes',
				'action' => 'view',
				'type' => $comment['Node']['type'],
				'slug' => $comment['Node']['slug'],
			)),
			$this->Html->link($this->Html->image('/img/icons/comment.png'), '#',
				array(
					'class' => 'comment-view',
					'data-title' => $title,
					'data-content' => $comment['Comment']['body'],
					'escape' => false
				)),
			$comment['Comment']['created'],
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
?>

</table>
	<div class="row-fluid">
		<div id="bulk-action" class="control-group">
			<?php
				echo $this->Form->input('Comment.action', array(
					'label' => false,
					'div' => 'input inline',
					'options' => array(
						'publish' => __('Publish'),
						'unpublish' => __('Unpublish'),
						'delete' => __('Delete'),
					),
					'empty' => true,
				));
			?>
			<div class="controls">
			<?php echo $this->Form->end(__('Submit')); ?>
			</div>
	</div>
</div>

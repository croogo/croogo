<?php

if (!$this->request->is('ajax') && isset($this->request->params['admin'])):
	$this->Html->script('Comments.admin', array('inline' => false));
endif;

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Comments'), array('plugin' => 'comments', 'controller' => 'comments', 'action' => 'index'));

if (isset($criteria['Comment.status'])) {
	if ($criteria['Comment.status'] == '1') {
		$this->Html->addCrumb(__d('croogo', 'Published'), '/' . $this->request->url);
		$this->viewVars['title_for_layout'] = __d('croogo', 'Comments: Published');
	} else {
		$this->Html->addCrumb(__d('croogo', 'Approval'), '/' . $this->request->url);
		$this->viewVars['title_for_layout'] = __d('croogo', 'Comments: Approval');
	}
}

echo $this->element('admin/modal', array(
	'id' => 'comment-modal',
	'class' => 'hide',
));

?>
<?php $this->start('actions'); ?>
<?php
	echo $this->Croogo->adminAction(
		__d('croogo', 'Published'),
		array('action' => 'index', '?' => array('status' => '1'))
	);
	echo $this->Croogo->adminAction(
		__d('croogo', 'Approval'),
		array('action' => 'index', '?' => array('status' => '0'))
	);
?>
<?php $this->end(); ?>

<?php echo $this->Form->create('Comment', array('url' => array('controller' => 'comments', 'action' => 'process'))); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('name', __d('croogo', 'Name')),
		$this->Paginator->sort('email', __d('croogo', 'Email')),
		$this->Paginator->sort('node_id', __d('croogo', 'Node')),
		'',
		$this->Paginator->sort('created', __d('croogo', 'Created')),
		__d('croogo', 'Actions'),
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
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Comment' . $comment['Comment']['id'] . 'Id',
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item'), 'rowAction' => 'delete'),
			__d('croogo', 'Are you sure?')
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
			$this->Html->link($this->Html->image('/croogo/img/icons/comment.png'), '#',
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
					'publish' => __d('croogo', 'Publish'),
					'unpublish' => __d('croogo', 'Unpublish'),
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

<?php $this->extend('/Common/admin_index'); ?>

<?php
	$this->Html
		->addCrumb($this->Html->icon('home'), '/admin')
		->addCrumb(__('Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
		->addCrumb(__('Comments'));
?>


<?php $this->start('tabs'); ?>
	<li>
		<?php
			echo $this->Html->link(
				__('Published'),
				array('action'=>'index', 'filter' => 'status:1;'),
				array('button' => 'default')
			);
		?>
	</li>
	<li>
		<?php
			echo $this->Html->link(
				__('Approval'),
				array('action'=>'index', 'filter' => 'status:0;'),
				array('button' => 'default')
			);
		?>
	</li>
<?php $this->end(); ?>


<?php
if (isset($this->params['named'])) {
	foreach ($this->params['named'] as $named => $value) {
		$this->Paginator->options['url'][$named] = $value;
	}
}
?>


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
			array('icon' => 'trash', 'tooltip' => __('Remove this item')),
			__('Are you sure?')
		);

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

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
			$this->Html->link($this->Html->image('/img/icons/comment.png'), '#', array('class' => 'tooltip', 'title' => $comment['Comment']['body'], 'escape' => false)),
			$comment['Comment']['created'],
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
?>

</table>
	<div class="row-fluid">
		<div class="control-group">
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

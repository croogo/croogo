<?php $this->extend('/Common/admin_index'); ?>

<?php $this->start('tabs'); ?>
	<li><?php echo $this->Html->link(__('Published'), array('action'=>'index', 'filter' => 'status:1;')); ?></li>
	<li><?php echo $this->Html->link(__('Approval'), array('action'=>'index', 'filter' => 'status:0;')); ?></li>
<?php $this->end(); ?>


<?php
if (isset($this->params['named'])) {
	foreach ($this->params['named'] AS $named => $value) {
		$this->Paginator->options['url'][$named] = $value;
	}
}
?>


<?php echo $this->Form->create('Comment', array('url' => array('controller' => 'comments', 'action' => 'process'))); ?>
<table cellpadding="0" cellspacing="0">
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
	echo $tableHeaders;

	$rows = array();
	foreach ($comments AS $comment) {
		$actions  = $this->Html->link(__('Edit'), array('action' => 'edit', $comment['Comment']['id']));
		$actions .= ' ' . $this->Layout->adminRowActions($comment['Comment']['id']);
		$actions .= ' ' . $this->Layout->processLink(__('Delete'),
			'#Comment' . $comment['Comment']['id'] . 'Id',
			null, __('Are you sure?'));

		$rows[] = array(
			$this->Form->checkbox('Comment.'.$comment['Comment']['id'].'.id'),
			$comment['Comment']['id'],
			//$comment['Comment']['title'],
			$comment['Comment']['name'],
			$comment['Comment']['email'],
			$this->Html->link($comment['Node']['title'], array(
				'admin' => false,
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
	echo $tableHeaders;
?>
</table>
<div class="bulk-actions">
<?php
	echo $this->Form->input('Comment.action', array(
		'label' => false,
		'options' => array(
			'publish' => __('Publish'),
			'unpublish' => __('Unpublish'),
			'delete' => __('Delete'),
		),
		'empty' => true,
	));
	echo $this->Form->end(__('Submit'));
?>
</div>

<div class="comments index">
	<h2><?php echo $title_for_layout; ?></h2>

	<?php
	if (isset($this->params['named'])) {
		foreach ($this->params['named'] AS $named => $value) {
			$paginator->options['url'][$named] = $value;
		}
	}
	?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Published', true), array('action'=>'index', 'filter' => 'status:1;')); ?></li>
			<li><?php echo $this->Html->link(__('Approval', true), array('action'=>'index', 'filter' => 'status:0;')); ?></li>
		</ul>
	</div>

	<?php echo $this->Form->create('Comment', array('url' => array('controller' => 'comments', 'action' => 'process'))); ?>
	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders = $this->Html->tableHeaders(array(
			'',
			$paginator->sort('id'),
			//$paginator->sort('title'),
			$paginator->sort('name'),
			$paginator->sort('email'),
			$paginator->sort('node_id'),
			'',
			$paginator->sort('created'),
			__('Actions', true),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($comments AS $comment) {
			$actions  = $this->Html->link(__('Edit', true), array('action' => 'edit', $comment['Comment']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($comment['Comment']['id']);
			$actions .= ' ' . $this->Html->link(__('Delete', true), array(
				'action' => 'delete',
				$comment['Comment']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?', true));

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
				'publish' => __('Publish', true),
				'unpublish' => __('Unpublish', true),
				'delete' => __('Delete', true),
			),
			'empty' => true,
		));
		echo $this->Form->end(__('Submit', true));
	?>
	</div>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>

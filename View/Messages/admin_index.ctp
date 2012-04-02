<?php $this->extend('/Common/admin_index'); ?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link(__('Unread'), array('action'=>'index', 'filter' => 'status:0;')); ?></li>
<li><?php echo $this->Html->link(__('Read'), array('action'=>'index', 'filter' => 'status:1;')); ?></li>
<?php $this->end(); ?>

<?php
	if (isset($this->params['named'])) {
		foreach ($this->params['named'] AS $nn => $nv) {
			$this->Paginator->options['url'][] = $nn . ':' . $nv;
		}
	}
?>

<?php echo $this->Form->create('Message', array('url' => array('controller' => 'messages', 'action' => 'process'))); ?>
<table cellpadding="0" cellspacing="0">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		$this->Paginator->sort('id'),
		$this->Paginator->sort('contact_id'),
		$this->Paginator->sort('name'),
		$this->Paginator->sort('email'),
		$this->Paginator->sort('title'),
		'',
		__('Actions'),
	));
	echo $tableHeaders;

	$rows = array();
	foreach ($messages AS $message) {
		$actions  = $this->Html->link(__('Edit'), array('action' => 'edit', $message['Message']['id']));
		$actions .= ' ' . $this->Layout->adminRowActions($message['Message']['id']);
		$actions .= ' ' . $this->Layout->processLink(__('Delete'),
			'#Message' . $message['Message']['id'] . 'Id',
			null, __('Are you sure?'));

		$rows[] = array(
			$this->Form->checkbox('Message.'.$message['Message']['id'].'.id'),
			$message['Message']['id'],
			$message['Contact']['title'],
			$message['Message']['name'],
			$message['Message']['email'],
			$message['Message']['title'],
			$this->Html->image('/img/icons/comment.png'),
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
	echo $tableHeaders;
?>
</table>
<div class="bulk-actions">
<?php
	echo $this->Form->input('Message.action', array(
		'label' => false,
		'options' => array(
			'read' => __('Mark as read'),
			'unread' => __('Mark as unread'),
			'delete' => __('Delete'),
		),
		'empty' => true,
	));
	echo $this->Form->end(__('Submit'));
?>
</div>

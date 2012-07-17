<?php $this->extend('/Common/admin_index'); ?>

<table cellpadding="0" cellspacing="0">
<?php
	$tableHeaders =  $this->Html->tableHeaders(array(
		$this->Paginator->sort('id'),
		$this->Paginator->sort('title'),
		$this->Paginator->sort('native'),
		$this->Paginator->sort('alias'),
		$this->Paginator->sort('status'),
		__('Actions'),
	));
	echo $tableHeaders;

	$rows = array();
	foreach ($languages AS $language) {
		$actions  = $this->Html->link(__('Move up'), array('action' => 'moveup', $language['Language']['id']));
		$actions .= ' ' . $this->Html->link(__('Move down'), array('action' => 'movedown', $language['Language']['id']));
		$actions .= ' ' . $this->Html->link(__('Edit'), array('action' => 'edit', $language['Language']['id']));
		$actions .= ' ' . $this->Layout->adminRowActions($language['Language']['id']);
		$actions .= ' ' . $this->Form->postLink(__('Delete'), array(
			'action' => 'delete',
			$language['Language']['id'],
		), null, __('Are you sure?'));

		$rows[] = array(
			$language['Language']['id'],
			$language['Language']['title'],
			$language['Language']['native'],
			$language['Language']['alias'],
			$this->Layout->status($language['Language']['status']),
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
	echo $tableHeaders;
?>
</table>

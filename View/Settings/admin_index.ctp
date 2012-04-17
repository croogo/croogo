<?php $this->extend('/Common/admin_index'); ?>

<table cellpadding="0" cellspacing="0">
<?php
	$tableHeaders =  $this->Html->tableHeaders(array(
		$this->Paginator->sort('id'),
		$this->Paginator->sort('key'),
		$this->Paginator->sort('value'),
		$this->Paginator->sort('editable'),
		__('Actions'),
	));
	echo $tableHeaders;

	$rows = array();
	foreach ($settings AS $setting) {
		$actions  = $this->Html->link(__('Move up'), array('controller' => 'settings', 'action' => 'moveup', $setting['Setting']['id']));
		$actions .= ' ' . $this->Html->link(__('Move down'), array('controller' => 'settings', 'action' => 'movedown', $setting['Setting']['id']));
		$actions .= ' ' . $this->Html->link(__('Edit'), array('controller' => 'settings', 'action' => 'edit', $setting['Setting']['id']));
		$actions .= ' ' . $this->Layout->adminRowActions($setting['Setting']['id']);
		$actions .= ' ' . $this->Form->postLink(__('Delete'), array(
			'controller' => 'settings',
			'action' => 'delete',
			$setting['Setting']['id'],
		), null, __('Are you sure?'));

		$key = $setting['Setting']['key'];
		$keyE = explode('.', $key);
		$keyPrefix = $keyE['0'];
		if (isset($keyE['1'])) {
			$keyTitle = '.' . $keyE['1'];
		} else {
			$keyTitle = '';
		}

		$rows[] = array(
			$setting['Setting']['id'],
			$this->Html->link($keyPrefix, array('controller' => 'settings', 'action' => 'index', 'p' => $keyPrefix)) . $keyTitle,
			$this->Text->truncate($setting['Setting']['value'], 20),
			$setting['Setting']['editable'],
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
	echo $tableHeaders;
?>
</table>

<?php

$this->extend('Croogo/Croogo./Common/admin_index');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Settings'), array(
		'prefix' => 'admin',
		'plugin' => 'Croogo/Settings',
		'controller' => 'Settings',
		'action' => 'index',
	));
if (!empty($this->request->params['named']['p'])) {
	$this->Html->addCrumb($this->request->params['named']['p']);
}
?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('key', __d('croogo', 'Key')),
		$this->Paginator->sort('value', __d('croogo', 'Value')),
		$this->Paginator->sort('editable', __d('croogo', 'Editable')),
		__d('croogo', 'Actions'),
	));
?>
	<thead>
	<?php echo $tableHeaders; ?>
	</thead>
<?php
	$rows = array();
	foreach ($settings as $setting):
		$actions = array();
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'Settings', 'action' => 'moveup', $setting->id),
			array('icon' => 'chevron-up', 'tooltip' => __d('croogo', 'Move up'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'Settings', 'action' => 'movedown', $setting->id),
			array('icon' => 'chevron-down', 'tooltip' => __d('croogo', 'Move down'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'Settings', 'action' => 'edit', $setting->id),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowActions($setting->id);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'Settings', 'action' => 'delete', $setting->id),
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));

		$key = $setting->key;
		$keyE = explode('.', $key);
		$keyPrefix = $keyE['0'];
		if (isset($keyE['1'])) {
			$keyTitle = '.' . $keyE['1'];
		} else {
			$keyTitle = '';
		}
		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		$rows[] = array(
			$setting->id,
			$this->Html->link($keyPrefix, array('controller' => 'Settings', 'action' => 'index', '?' => array('key' => $keyPrefix))) . $keyTitle,
			$this->Text->truncate($setting->value, 20),
			$this->CroogoHtml->status($setting->editable),
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);
?>
</table>

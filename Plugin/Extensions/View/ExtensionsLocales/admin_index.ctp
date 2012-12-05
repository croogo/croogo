<?php

$this->extend('Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__('Locales'), $this->here);

?>
<?php echo $this->start('actions') ?>
<li>
<?php
	echo $this->Html->link(__('Upload'),
		array('action' => 'add'),
		array('button' => 'default')
	);
?>
</li>
<?php echo $this->end('actions') ?>

<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__('Locale'),
		__('Default'),
		__('Actions'),
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>

<?php 
	$rows = array();
	foreach ($locales AS $locale):
		$actions = array();

		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'activate', $locale),
			array('icon' => 'bolt', 'tooltip' => __('Activate'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $locale),
			array('icon' => 'pencil', 'tooltip' => __('Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete', $locale),
			array('icon' => 'trash', 'tooltip' => __('Remove this item')),
			__('Are you sure?')
		);

		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		if ($locale == Configure::read('Site.locale')) {
			$status = $this->Html->status(1);
		} else {
			$status = $this->Html->status(0);
		}

		$rows[] = array(
			'',
			$locale,
			$status,
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);
?>
</table>

<?php

$this->extend('Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Locales'), '/' . $this->request->url);

?>
<?php echo $this->start('actions') ?>
<?php
	echo $this->Croogo->adminAction(__d('croogo', 'Upload'),
		array('action' => 'add')
	);
?>
<?php echo $this->end('actions') ?>

<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__d('croogo', 'Locale'),
		__d('croogo', 'Default'),
		__d('croogo', 'Actions'),
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>

<?php
	$rows = array();
	foreach ($locales as $locale):
		$actions = array();

		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'activate', $locale),
			array('icon' => 'bolt', 'tooltip' => __d('croogo', 'Activate'), 'method' => 'post')
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $locale),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete', $locale),
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?')
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

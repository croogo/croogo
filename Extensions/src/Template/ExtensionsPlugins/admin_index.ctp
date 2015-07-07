<?php

$this->extend('/Common/admin_index');

$this->name = 'extensions-plugins';

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Plugins'), '/' . $this->request->url);

?>
<?php $this->start('actions'); ?>
<?php
	echo $this->Croogo->adminAction(
		__d('croogo', 'Upload'),
		array('action' => 'add')
	);
?>
<?php $this->end(); ?>

<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__d('croogo', 'Alias'),
		__d('croogo', 'Name'),
		__d('croogo', 'Description'),
		__d('croogo', 'Active'),
		__d('croogo', 'Actions'),
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>

<?php
	$rows = array();
	$plugins = Sanitize::clean($plugins);
	foreach ($plugins as $pluginAlias => $pluginData):
		$toggleText = $pluginData['active'] ? __d('croogo', 'Deactivate') : __d('croogo', 'Activate');
		$statusIcon = $this->Html->status($pluginData['active']);

		$actions = array();
		if (!in_array($pluginAlias, $bundledPlugins) && !in_array($pluginAlias, $corePlugins)):
			$icon = $pluginData['active'] ? $this->Theme->getIcon('power-off') : $this->Theme->getIcon('power-on');
			$actions[] = $this->Croogo->adminRowAction('',
				array('action' => 'toggle',	$pluginAlias),
				array('icon' => $icon, 'tooltip' => $toggleText, 'method' => 'post')
			);
			$actions[] = $this->Croogo->adminRowAction('',
				array('action' => 'delete', $pluginAlias),
				array('icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Delete')),
				__d('croogo', 'Are you sure?')
			);
		endif;

		if ($pluginData['active'] && !in_array($pluginAlias, $bundledPlugins) && !in_array($pluginAlias, $corePlugins)) {
			$actions[] = $this->Croogo->adminRowAction('',
				array('action' => 'moveup', $pluginAlias),
				array('icon' => $this->Theme->getIcon('move-up'), 'tooltip' => __d('croogo', 'Move up'), 'method' => 'post'),
				__d('croogo', 'Are you sure?')
			);

			$actions[] = $this->Croogo->adminRowAction('',
				array('action' => 'movedown', $pluginAlias),
				array('icon' => $this->Theme->getIcon('move-down'), 'tooltip' => __d('croogo', 'Move down'), 'method' => 'post'),
				__d('croogo', 'Are you sure?')
			);
		}

		if ($pluginData['needMigration']) {
			$actions[] = $this->Croogo->adminRowAction(__d('croogo', 'Migrate'), array(
				'action' => 'migrate',
				$pluginAlias,
			), array(), __d('croogo', 'Are you sure?'));
		}

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$rows[] = array(
			'',
			$pluginAlias,
			$pluginData['name'],
			!empty($pluginData['description']) ? $pluginData['description'] : '',
			$statusIcon,
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);
?>
</table>

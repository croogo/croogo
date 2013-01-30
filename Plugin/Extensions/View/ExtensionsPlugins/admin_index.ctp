<?php
$this->extend('/Common/admin_index');
$this->name = 'extensions-plugins';

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__('Plugins'), $this->here);

?>
<?php $this->start('actions'); ?>
<li>
	<?php
		echo $this->Html->link(
			__('Upload'),
			array('action' => 'add'),
			array('button' => 'default')
		);
	?>
</li>
<?php $this->end(); ?>

<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__('Alias'),
		__('Name'),
		__('Description'),
		__('Active'),
		__('Actions'),
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>

<?php
	$rows = array();
	foreach ($plugins AS $pluginAlias => $pluginData):
		if (in_array($pluginAlias, $corePlugins)) {
			continue;
		}

		$toggleText = $pluginData['active'] ? __('Deactivate') : __('Activate');
		$iconImage = $this->Html->status($pluginData['active']);
		$icon = $pluginData['active'] ? 'off' : 'bolt';

		$actions  = array();
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'toggle',	$pluginAlias),
			array('icon' => $icon, 'tooltip' => $toggleText, 'method' => 'post')
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete', $pluginAlias),
			array('icon' => 'trash', 'tooltip' => __('Delete')),
			__('Are you sure?')
		);

		if ($pluginData['needMigration']) {
			$actions[] = $this->Croogo->adminRowAction(__('Migrate'), array(
				'action' => 'migrate',
				$pluginAlias,
			), array(), __('Are you sure?'));
		}

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$rows[] = array(
			'',
			$pluginAlias,
			$pluginData['name'],
			$pluginData['description'],
			$this->Form->postLink($iconImage, array(
				'action' => 'toggle',
				$pluginAlias,
			), array(
				'escape' => false,
			)),
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);
?>
</table>

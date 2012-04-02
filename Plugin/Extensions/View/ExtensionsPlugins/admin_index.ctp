<?php
$this->extend('/Common/admin_index');
$this->name = 'extensions-plugins';
?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link(__('Upload'), array('action' => 'add')); ?></li>
<?php $this->end(); ?>

<table cellpadding="0" cellspacing="0">
<?php
	$tableHeaders =  $this->Html->tableHeaders(array(
		'',
		__('Alias'),
		__('Name'),
		__('Description'),
		__('Active'),
		__('Actions'),
	));
	echo $tableHeaders;

	$rows = array();
	foreach ($plugins AS $pluginAlias => $pluginData) {
		if (in_array($pluginAlias, $corePlugins)) {
			continue;
		}

		if ($pluginData['active']) {
			$icon = 'tick.png';
			$toggleText = __('Deactivate');
		} else {
			$icon = 'cross.png';
			$toggleText = __('Activate');
		}
		$iconImage = $this->Html->image('icons/'.$icon);

		$actions  = '';
		$actions .= ' ' . $this->Form->postLink($toggleText, array(
			'action' => 'toggle',
			$pluginAlias,
		));
		$actions .= ' ' . $this->Form->postLink(__('Delete'), array(
			'action' => 'delete',
			$pluginAlias,
		), null, __('Are you sure?'));

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
	}

	echo $this->Html->tableCells($rows);
	echo $tableHeaders;
?>
</table>

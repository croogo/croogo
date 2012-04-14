<?php
if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
?>
<div class="<?php echo strtolower($this->name); ?> index">
	<h2><?php if ($this->fetch('title')): ?>
		<?php echo $this->fetch('title'); ?>
	<?php else: ?>
		<?php
		echo !empty($title_for_layout) ? $title_for_layout : $this->name;
		?>
	<?php endif; ?></h2>

	<div class="actions">
		<ul>
			<?php if ($this->fetch('tabs')): ?>
				<?php echo $this->fetch('tabs'); ?>
			<?php else: ?>
				<li><?php echo $this->Html->link(
					'New ' . Inflector::singularize($this->name),
					array('action' => 'add')
				); ?></li>
			<?php endif; ?>
		</ul>
	</div>

	<?php if ($this->fetch('content')): ?>
		<?php echo $this->fetch('content'); ?>
	<?php else: ?>
		<table cellpadding="0" cellspacing="0">
		<?php
		$tableHeaders = array();
		foreach ($displayFields as $field => $arr) {
			if ($arr['sort']) {
				$tableHeaders[] = $this->Paginator->sort($field, $arr['label']);
			} else {
				$tableHeaders[] = $arr['label'];
			}
		}
		$tableHeaders[] = __('Actions');
		$tableHeaders = $this->Html->tableHeaders($tableHeaders);

		$rows = array();
		if (!empty(${strtolower($this->name)})) {
			foreach (${strtolower($this->name)} as $item) {
				$actions  = $this->Html->link(
					__('Edit'),
					array('action' => 'edit', $item[$modelClass]['id'])
				);
				$actions .= '&nbsp;' . $this->Layout->adminRowActions($item[$modelClass]['id']);
				$actions .= '&nbsp;' . $this->Html->link(__('Delete'), array(
					'action' => 'delete',
					$item[$modelClass]['id'],
				), null, __('Are you sure?'));
				$row = array();
				foreach ($displayFields as $key => $val) {
					if (!is_int($key)) {
						$val = $key;
					}
					if (strpos($val, '.') === false) {
						$val = $modelClass . '.' . $val;
					}
					list($model, $field) = pluginSplit($val);
					$row[] = $item[$model][$field];
				}
				$row[] = $actions;
				$rows[] = $row;
			}
		}

		echo $tableHeaders;
		echo $this->Html->tableCells($rows);
		echo $tableHeaders;
		?>
		</table>
	<?php endif; ?>
</div>

<?php if ($this->fetch('paging')): ?>
	<?php echo $this->fetch('paging'); ?>
<?php else: ?>
	<?php if (isset($this->Paginator)): ?>
		<div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
		<div class="counter"><?php echo $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?></div>
	<?php endif; ?>
<?php endif; ?>
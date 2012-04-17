<?php
if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}
?>
<div class="<?php echo $className; ?> index">
	<h2><?php if ($titleBlock = $this->fetch('title')): ?>
		<?php echo $titleBlock; ?>
	<?php else: ?>
		<?php
		echo !empty($title_for_layout) ? $title_for_layout : $this->name;
		?>
	<?php endif; ?></h2>

	<div class="actions">
		<ul>
			<?php if ($tabsBlock = $this->fetch('tabs')): ?>
				<?php echo $tabsBlock; ?>
			<?php else: ?>
			<li>
				<?php
				echo $this->Html->link(__('New %s', Inflector::singularize($this->name)),
					array('action' => 'add')
				);
				?>
			</li>
			<?php endif; ?>
		</ul>
	</div>

	<?php if ($contentBlock = $this->fetch('content')): ?>
		<?php echo $contentBlock; ?>
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
				$actions .= '&nbsp;' . $this->Form->postLink(__('Delete'), array(
					'action' => 'delete',
					$item[$modelClass]['id'],
				), null, __('Are you sure?'));
				$row = array();
				foreach ($displayFields as $key => $val) {
					extract($val);
					if (!is_int($key)) {
						$val = $key;
					}
					if (strpos($val, '.') === false) {
						$val = $modelClass . '.' . $val;
					}
					list($model, $field) = pluginSplit($val);
					$row[] = $this->Layout->displayField($item, $model, $field, compact('type', 'url', 'options'));
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

<?php if ($pagingBlock = $this->fetch('paging')): ?>
	<?php echo $pagingBlock; ?>
<?php else: ?>
	<?php if (isset($this->Paginator)): ?>
		<div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
		<div class="counter"><?php echo $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?></div>
	<?php endif; ?>
<?php endif; ?>
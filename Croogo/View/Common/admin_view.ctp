<?php

if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}

$rowClass = $this->Layout->cssClass('row');
$columnFull = $this->Layout->cssClass('columnFull');
$tableClass = isset($tableClass) ? $tableClass : $this->Layout->cssClass('tableClass');

$showActions = isset($showActions) ? $showActions : true;

if ($pageHeading = trim($this->fetch('page-heading'))):
	echo $pageHeading;
endif;
?>

<h2 class="hidden-desktop">
	<?php if ($titleBlock = $this->fetch('title')): ?>
		<?php echo $titleBlock; ?>
	<?php else: ?>
		<?php
		echo !empty($title_for_layout) ? $title_for_layout : $this->name;
		?>
	<?php endif; ?>
</h2>

<?php if ($showActions): ?>
<div class="<?php echo $rowClass; ?>">
	<div class="actions <?php echo $columnFull; ?>">
		<?php
		if ($actionsBlock = $this->fetch('actions')):
			echo $actionsBlock;
		else:
			echo $this->Croogo->adminAction(
				__d('croogo', 'New %s', __d('croogo', Inflector::singularize($this->name))),
				array('action' => 'add'),
				array('button' => 'success')
			);
		endif;
		?>
	</div>
</div>
<?php endif; ?>

<div class="<?php echo $rowClass; ?>">
	<div class="<?php echo $columnFull; ?>">
	<?php
		if ($contentBlock = trim($this->fetch('content'))):
			echo $this->element('admin/search');
			echo $contentBlock;
		else:
			if ($mainBlock = trim($this->fetch('main'))):
				echo $mainBlock;
			endif;
		endif;
	?>
	</div>
</div>

<?php

if ($pageFooter = trim($this->fetch('page-footer'))):
	echo $pageFooter;
endif;

<?php

if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}

$what = isset($this->request->data[$modelClass]['id']) ?
	__d('croogo', 'Edit') :
	__d('croogo', 'Add');

$cssClass = $this->Theme->getCssClass('row');
$columnLeft = $this->Theme->getCssClass('columnLeft');
$columnRight = $this->Theme->getCssClass('columnRight');
$columnFull = $this->Theme->getCssClass('columnFull');

?>
<h2 class="hidden-desktop">
<?php
	if ($titleBlock = $this->fetch('title')):
		echo $titleBlock;
	else:
		echo !empty($title_for_layout) ? $title_for_layout : $what . ' ' . $modelClass;
	endif;
?>
</h2>
<?php

if ($pageHeading = trim($this->fetch('page-heading'))):
	echo $pageHeading;
endif;

?>
<?php if ($actionsBlock = $this->fetch('actions')): ?>
<div class="<?php echo $cssClass; ?>">
	<div class="actions <?php echo $columnFull; ?>">
		<div class="btn-group">
			<?php echo $actionsBlock; ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php

if ($contentBlock = trim($this->fetch('content'))):
	echo $contentBlock;
	return;
endif;

if ($formStart = trim($this->fetch('form-start'))):
	echo $formStart;
else:
	echo $this->Form->create($modelClass);
	if (isset($this->request->data[$modelClass]['id'])):
		echo $this->Form->input('id');
	endif;
endif;

$tabId = 'tabitem-' . Inflector::slug(strtolower($modelClass), '-');

?>
<div class="<?php echo $cssClass; ?>">
	<div class="<?php echo $columnLeft; ?>">

		<ul class="nav nav-tabs">
		<?php
			if ($tabHeading = $this->fetch('tab-heading')):
				echo $tabHeading;
			else:
				echo $this->Croogo->adminTab(__d('croogo', $modelClass), "#$tabId");
				echo $this->Croogo->adminTabs();
			endif;
		?>
		</ul>

		<?php

		$tabContent = trim($this->fetch('tab-content'));
		if (!$tabContent):
			$content = '';
			foreach ($editFields as $field => $opts):
				if (is_string($opts)) {
					$field = $opts;
					$opts = array(
						'label' => false,
						'tooltip' => ucfirst($field),
					);
				}
				$content .= $this->Form->input($field, $opts);
			endforeach;
		endif;
		?>

		<?php
		if (empty($tabContent) && !empty($content)):
			$tabContent = $this->Html->div('tab-pane', $content, array(
				'id' => $tabId,
			));
			$tabContent .= $this->Croogo->adminTabs();
		endif;
		echo $this->Html->div('tab-content', $tabContent);
		?>
	</div>

	<div class="<?php echo $columnRight; ?>">
	<?php
		if ($rightCol = $this->fetch('panels')):
			echo $rightCol;
		else:
			if ($buttonsBlock = $this->fetch('buttons')):
				echo $buttonsBlock;
			else:
				echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
					$this->Form->button(__d('croogo', 'Save'), array('button' => 'primary')) .
					$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger')) .
					$this->Html->endBox();
			endif;
			echo $this->Croogo->adminBoxes();
		endif;
	?>
	</div>

</div>
<?php

if ($formEnd = trim($this->fetch('form-end'))):
	echo $formEnd;
else:
	echo $this->Form->end();
endif;

if ($pageFooter = trim($this->fetch('page-footer'))):
	echo $pageFooter;
endif;

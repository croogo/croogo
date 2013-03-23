<?php
if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}
$what = isset($this->request->data[$modelClass]['id']) ? __d('croogo', 'Edit') : __d('croogo', 'Add');
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

<?php if ($actionsBlock = $this->fetch('actions')): ?>
<div class="row-fluid">
	<div class="span12 actions">
		<ul class="nav-buttons">
			<?php echo $actionsBlock; ?>
		</ul>
	</div>
</div>
<?php endif; ?>

<?php if ($contentBlock = $this->fetch('content')): ?>
	<?php echo $contentBlock; ?>
<?php else: ?>
	<?php
		$tabId = 'tabitem-' . Inflector::slug(strtolower($modelClass), '-');
		echo $this->Form->create($modelClass);
		if (isset($this->request->data[$modelClass]['id'])) {
			echo $this->Form->input('id');
		}
	?>
	<div class="row-fluid">
		<div class="span8">
			<ul class="nav nav-tabs">
			<?php
				echo $this->Croogo->adminTab(__d('croogo', $modelClass), "#$tabId");
				echo $this->Croogo->adminTabs();
			?>
			</ul>

			<?php
				$content = '';
				foreach ($editFields as $field => $opts):
					if (is_string($opts)) {
						$field = $opts;
						$opts = array(
							'class' => 'span10',
							'label' => false,
							'tooltip' => ucfirst($field),
						);
					} else {
						$opts = Hash::merge(array('class' => 'span10'), $opts);
					}
					$content .= $this->Form->input($field, $opts);
				endforeach;
			?>

			<div class="tab-content">
			<?php
				if (!empty($content)):
					echo $this->Html->div('tab-pane', $content, array(
						'id' => $tabId,
					));
				endif;
				echo $this->Croogo->adminTabs();
			?>
			</div>
		</div>
		<div class="span4">
		<?php
			if ($buttonsBlock = $this->fetch('buttons')):
				$publishing = $buttonsBlock;
			else :
				echo $this->Html->beginBox('Publishing') .
					$this->Form->button(__d('croogo', 'Save'), array('button' => 'primary')) .
					$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger')) .
					$this->Html->endBox();

			endif;
			echo $this->Croogo->adminBoxes();
		?>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>
<?php endif; ?>

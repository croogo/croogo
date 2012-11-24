<?php
if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}
$what = isset($this->request->data[$modelClass]['id']) ? __('Edit') : __('Add');
?>
	<h2 class="hidden-desktop">
	<?php
	if ($titleBlock = $this->fetch('title')):
		echo $titleBlock;
	else:
		echo !empty($title_for_layout) ? $title_for_layout : $what . ' ' . $modelClass;
	?>
	<?php endif; ?>
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
		<?php echo $this->Form->create($modelClass); ?>
		<?php
		if (isset($this->request->data[$modelClass]['id'])) {
			echo $this->Form->input('id');
		}
		?>
		<div class="row-fluid">
			<div class="span8">
				<?php
					$content = '';
					foreach ($editFields as $field => $opts):
						$_opts = array('class' => 'span12');
						$content .= $this->Form->input($field, $opts);
					endforeach;

					if (!empty($content)):
						echo $this->Html->beginBox($modelClass) .
							$content .
							$this->Html->endBox();
					endif;
					echo $this->Croogo->adminBoxes();
				?>
			</div>
			<div class="span4">
				<?php if ($buttonsBlock = $this->fetch('buttons')): ?>
					<?php $publishing = $buttonsBlock; ?>
				<?php else : ?>
					<?php
					echo $this->Html->beginBox('Publishing') .
							$this->Form->button(__('Save'), array('button' => 'primary')) .
							$this->Html->link(__('Cancel'), array('action' => 'index'), array('button' => 'danger')) .
							$this->Html->endBox();
					?>
				<?php endif; ?>
			</div>
		</div>
		<?php echo $this->Form->end(); ?>
	<?php endif; ?>

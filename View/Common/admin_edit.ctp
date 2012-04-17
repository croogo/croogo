<?php
if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}
$what = isset($this->request->data[$modelClass]['id']) ? __('Edit') : __('Add');
?>
<div class="<?php echo $className; ?> form">
	<h2><?php if ($titleBlock = $this->fetch('title')): ?>
		<?php echo $titleBlock; ?>
	<?php else: ?>
		<?php
		echo !empty($title_for_layout) ? $title_for_layout : $what . ' ' . $modelClass;
		?>
	<?php endif; ?></h2>

	<?php if ($actionsBlock = $this->fetch('actions')): ?>
	<div class="actions">
		<ul>
			<?php echo $actionsBlock; ?>
		</ul>
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
		<fieldset>
			<div class="tabs">
				<ul>
					<li><a href="#<?php echo strtolower($modelClass); ?>-main"><?php echo $modelClass; ?></a></li>
					<?php echo $this->Layout->adminTabs(); ?>
				</ul>
				<div id="<?php echo strtolower($modelClass); ?>-main">
					<?php foreach ($editFields as $field => $opts): ?>
						<?php echo $this->Form->input($field, $opts); ?>
					<?php endforeach; ?>
				</div>
				<?php echo $this->Layout->adminTabs(); ?>
			</div>
		</fieldset>

		<div class="buttons">
			<?php if ($buttonsBlock = $this->fetch('buttons')): ?>
				<?php echo $buttonsBlock; ?>
			<?php else: ?>
				<?php
				echo $this->Form->end(__('Save'));
				echo $this->Html->link(__('Cancel'), array(
					'action' => 'index',
				), array(
					'class' => 'cancel',
				));
				?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
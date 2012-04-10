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
	<h2><?php if ($this->fetch('title')): ?>
		<?php echo $this->fetch('title'); ?>
	<?php else: ?>
		<?php
		echo !empty($title_for_layout) ? $title_for_layout : $what . ' ' . $modelClass;
		?>
	<?php endif; ?></h2>

	<div class="actions">
		<ul>
			<?php echo $this->fetch('actions'); ?>
		</ul>
	</div>

	<?php if ($this->fetch('content')): ?>
		<?php echo $this->fetch('content'); ?>
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
			<?php if ($this->fetch('buttons')): ?>
				<?php echo $this->fetch('buttons'); ?>
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
<div class="translate form">
	<h2><?php echo $title_for_layout; ?></h2>

<<<<<<< HEAD
	<?php
		echo $this->Form->create($modelAlias, array('url' => array(
			'controller' => 'translate',
			'action' => 'edit',
			$id,
			$modelAlias,
			'locale' => $this->params['named']['locale'],
		)));
	?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#record-main"><span><?php echo __('Record'); ?></span></a></li>
			</ul>
=======
	<?php
		echo $this->Form->create($modelAlias, array('url' => array(
			'controller' => 'translate',
			'action' => 'edit',
			$id,
			$modelAlias,
			'locale' => $this->params['named']['locale'],
		)));
	?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#record-main"><span><?php __('Record'); ?></span></a></li>
			</ul>
>>>>>>> 1.3-whitespace

			<div id="record-main">
			<?php
				foreach ($fields AS $field) {
					echo $this->Form->input($modelAlias.'.'.$field);
				}
			 ?>
			 </div>
		</div>
	</fieldset>

<<<<<<< HEAD
	<div class="buttons">
	<?php
		echo $this->Form->end(__('Save'));
	?>
	</div>
=======
	<div class="buttons">
	<?php
		echo $this->Form->end(__('Save', true));
	?>
	</div>
>>>>>>> 1.3-whitespace
</div>

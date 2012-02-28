<div class="attachments form">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="tabs">
		<ul>
			<li><a href="#attachment-main">Attachment</a></li>
			<?php echo $this->Layout->adminTabs(); ?>
		</ul>

		<div id="attachment-main">
		<?php
			$formUrl = array('controller' => 'attachments', 'action' => 'add');
			if (isset($this->params['named']['editor'])) {
				$formUrl['editor'] = 1;
			}
			echo $this->Form->create('Node', array('url' => $formUrl, 'type' => 'file'));
		?>
			<fieldset>
			<?php
				echo $this->Form->input('Node.file', array('label' => __('Upload'), 'type' => 'file',));
			?>
			</fieldset>
		</div>

		<?php echo $this->Layout->adminTabs(); ?>
		</div>
	</div>

	<div class="buttons">
	<?php
		echo $this->Form->end(__('Save'));
		echo $this->Html->link(__('Cancel'), array(
			'action' => 'index',
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
</div>
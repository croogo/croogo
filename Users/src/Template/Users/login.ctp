<div class="users form">
	<h2><?php echo __d('croogo', 'Login'); ?></h2>
	<?php echo $this->CroogoForm->create(false, array('url' => array('action' => 'login')));?>
		<fieldset>
		<?php
			echo $this->CroogoForm->input('username');
			echo $this->CroogoForm->input('password');
		?>
		</fieldset>
	<?php echo $this->CroogoForm->submit('Submit'); ?>
	<?php echo $this->CroogoForm->end();?>
</div>

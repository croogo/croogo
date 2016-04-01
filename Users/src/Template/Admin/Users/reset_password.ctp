<h2 class="hidden-md-up"><?php echo __d('croogo', 'Reset password'); ?>: <?php echo $user->username; ?></h2>
<?php
$this->CroogoHtml
	->addCrumb($this->CroogoHtml->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'))
	->addCrumb($user->name, array(
		'action' => 'edit', $user->id,
	))
	->addCrumb(__d('croogo', 'Reset Password'), '/' . $this->request->url);

$this->set('title_for_layout', __d('croogo', 'Reset Password for %s', $user->username));
?>
<?php echo $this->CroogoForm->create($user, array('url' => array('action' => 'reset_password')));?>
<div class="row-fluid">
	<div class="span8">
		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Reset Password'), '#reset-password');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">
			<div id="reset-password" class="tab-pane">
			<?php
				echo $this->CroogoForm->input('id');
				echo $this->CroogoForm->input('password', array('label' => __d('croogo', 'New Password'), 'value' => ''));
				echo $this->CroogoForm->input('verify_password', array('label' => __d('croogo', 'Verify Password'), 'type' => 'password', 'value' => ''));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>
	<div class="span4">
	<?php
		echo $this->CroogoHtml->beginBox(__d('croogo', 'Publishing')) .
			$this->CroogoForm->button(__d('croogo', 'Reset'), array('button' => 'default')) .
			$this->CroogoHtml->link(
				__d('croogo', 'Cancel'),
				array('action' => 'index'),
				array('button' => 'primary')) .
			$this->CroogoHtml->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->CroogoForm->end(); ?>

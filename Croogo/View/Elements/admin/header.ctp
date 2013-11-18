<?php

$dashboardUrl = array(
	'admin' => true,
	'plugin' => 'settings',
	'controller' => 'settings',
	'action' => 'dashboard',
);
?>
<header class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="btn btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<span class="hidden-phone">
			<?php echo $this->Html->link(Configure::read('Site.title'), $dashboardUrl, array('class' => 'brand')); ?>
			</span>
			<span class="hidden-desktop hidden-tablet">
			<?php echo $this->Html->link(__d('croogo', 'Dashboard'), $dashboardUrl, array('class' => 'brand')); ?>
			</span>
			<div class="nav-collapse collapse" style="height: 0px; ">
				<ul class="nav">
					<li>
						<?php echo $this->Html->link(__d('croogo', 'Visit website'), '/', array('target' => '_blank')); ?>
					</li>
				</ul>
				<?php if ($this->Session->read('Auth.User.id')): ?>
				<ul class="nav pull-right">
					<li>
						<a href="#">
							<?php echo __d('croogo', "You are logged in as: %s", $this->Session->read('Auth.User.username')); ?>
						</a>
					</li>
					<li>
						<?php echo $this->Html->link(__d('croogo', "Log out"), array('plugin' => 'users', 'controller' => 'users', 'action' => 'logout')); ?>
					</li>
				</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</header>
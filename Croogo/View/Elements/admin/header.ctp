<?php

$dashboardUrl = Configure::read('Croogo.dashboardUrl');

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
			<?php echo $this->Html->link(Configure::read('Site.title'), $dashboardUrl, array('class' => 'brand ellipsis')); ?>
			</span>
			<span class="hidden-desktop hidden-tablet">
			<?php echo $this->Html->link(__d('croogo', 'Dashboard'), $dashboardUrl, array('class' => 'brand')); ?>
			</span>
			<div class="nav-collapse collapse" style="height: 0px; ">
			<?php
				echo $this->Croogo->adminMenus(CroogoNav::items('top-left'), array(
					'type' => 'dropdown',
					'htmlAttributes' => array(
						'id' => 'top-left-menu',
						'class' => 'nav',
					),
				));
			?>
			<?php if ($this->Session->read('Auth.User.id')): ?>
			<?php
				echo $this->Croogo->adminMenus(CroogoNav::items('top-right'), array(
					'type' => 'dropdown',
					'htmlAttributes' => array(
						'id' => 'top-right-menu',
						'class' => 'nav pull-right',
					),
				));
			?>
			<?php endif; ?>
			</div>
		</div>
	</div>
</header>
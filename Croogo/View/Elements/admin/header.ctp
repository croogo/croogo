<?php

$dashboardUrl = Configure::read('Croogo.dashboardUrl');

?>
<header class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="<?php echo $this->Theme->getCssClass('container'); ?>">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only"><?php echo __d('croogo', 'Toggle navigation')?></span>
					<i class="fa fa-bars fa-lg"></i>
				</button>
				<span class="<?php echo $this->Theme->getCssClass('hiddenPhone'); ?>">
				<?php echo $this->Html->link(Configure::read('Site.title'), $dashboardUrl, array('class' => $this->Theme->getCssClass('brand'))); ?>
				</span>
				<span class="<?php echo $this->Theme->getCssClass('hiddenLarge'); ?>">
				<?php echo $this->Html->link(__d('croogo', 'Dashboard'), $dashboardUrl, array('class' => $this->Theme->getCssClass('brand'))); ?>
				</span>
			</div>
			<div class="collapse navbar-collapse" id="navbar-collapse">
			<?php
				echo $this->Croogo->adminMenus(CroogoNav::items('top-left'), array(
					'type' => 'dropdown',
					'htmlAttributes' => array(
						'id' => 'top-left-menu',
						'class' => 'nav navbar-nav',
					),
				));

			if ($this->Session->read('Auth.User.id')):
				echo $this->Croogo->adminMenus(CroogoNav::items('top-right'), array(
					'type' => 'dropdown',
					'htmlAttributes' => array(
						'id' => 'top-right-menu',
						'class' => 'navbar-nav nav navbar-right',
					),
				));
				endif;
			?>
			</div>
		</div>
	</div>
</header>
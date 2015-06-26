<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title><?php echo $title_for_layout; ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
		<?php

		echo $this->Html->css(array(
			'Croogo/Core.croogo-bootstrap',
			'Croogo/Core.croogo-bootstrap-responsive',
			'Croogo/Core.admin',
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'Croogo/Core.html5',
			'Croogo/Core.jquery/jquery.min',
			'Croogo/Core.jquery/jquery.slug',
			'Croogo/Core.croogo-bootstrap.js',
			'Croogo/Core.underscore-min',
			'Croogo/Core.admin',
		));

		echo $this->fetch('script');
		echo $this->fetch('css');

		?>
	</head>
	<body class="popup">
		<div class="<?php echo $this->Theme->getCssClass('container'); ?>">
			<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
				<div id="content" class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
					<?php echo $this->Layout->sessionFlash(); ?>
					<?php echo $this->fetch('content'); ?>
				</div>
			</div>
		</div>
		<?php
		echo $this->element('Croogo/Core.admin/initializers');
		echo $this->Blocks->get('scriptBottom');
		?>
	</body>
</html>

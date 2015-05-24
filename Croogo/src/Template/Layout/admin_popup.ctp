<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title><?php echo $title_for_layout; ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
		<?php

		echo $this->Html->css(array(
			'Croogo/Croogo.croogo-bootstrap',
			'Croogo/Croogo.croogo-bootstrap-responsive',
			'admin',
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'Croogo/Croogo.html5',
			'Croogo/Croogo.jquery/jquery.min',
			'Croogo/Croogo.jquery/jquery.slug',
			'Croogo/Croogo.croogo-bootstrap.js',
			'Croogo/Croogo.underscore-min',
			'Croogo/Croogo.admin',
		));

		echo $this->fetch('script');
		echo $this->fetch('css');

		?>
	</head>
	<body class="popup">
		<div class="container-fluid">
			<div class="row-fluid">
				<div id="content" class="span12">
					<?php echo $this->Layout->sessionFlash(); ?>
					<?php echo $this->fetch('content'); ?>
				</div>
			</div>
		</div>
		<?php
		echo $this->Blocks->get('scriptBottom');
		?>
	</body>
</html>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title><?php echo $title_for_layout; ?> - <?php echo __('Croogo'); ?></title>
		<?php

		echo $this->Html->css(array(
			'croogo-bootstrap',
			'croogo-bootstrap-responsive',
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'html5',
			'jquery/jquery.min',
			'jquery/jquery.slug',
			'croogo-bootstrap.js',
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
		echo $this->Js->writeBuffer();
		?>
	</body>
</html>
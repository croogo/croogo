<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title><?php echo $this->fetch('title'); ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
		<?php

		echo $this->Html->css(array(
			'Croogo/Croogo.croogo-bootstrap',
			'Croogo/Croogo.croogo-bootstrap-responsive',
			'Croogo/Croogo.thickbox',
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'Croogo/Croogo.html5',
			'Croogo/Croogo.jquery/jquery.min',
			'Croogo/Croogo.jquery/jquery-ui.min',
			'Croogo/Croogo.jquery/jquery.slug',
			'Croogo/Croogo.jquery/jquery.cookie',
			'Croogo/Croogo.jquery/jquery.hoverIntent.minified',
			'Croogo/Croogo.jquery/superfish',
			'Croogo/Croogo.jquery/supersubs',
			'Croogo/Croogo.jquery/jquery.tipsy',
			'Croogo/Croogo.jquery/jquery.elastic-1.6.1.js',
			'Croogo/Croogo.jquery/thickbox-compressed',
			'Croogo/Croogo.underscore-min',
			'Croogo/Croogo.admin',
			'Croogo/Croogo.choose',
			'Croogo/Croogo.typeahead_autocomplete',
			'Croogo/Croogo.croogo-bootstrap.js',
		));

		echo $this->fetch('script');
		echo $this->fetch('css');

		?>
	</head>
	<body>
		<div id="wrap">
			<?php echo $this->element('Croogo/Croogo.admin/header'); ?>
			<?php echo $this->element('Croogo/Croogo.admin/navigation'); ?>
			<div id="push"></div>
			<div id="content-container" class="container-fluid">
				<div class="row-fluid">
					<div id="content" class="clearfix">
						<?php echo $this->element('Croogo/Croogo.admin/breadcrumb'); ?>
						<div id="inner-content" class="span12">
							<?php echo $this->Flash->render(); ?>
							<?php echo $this->fetch('content'); ?>
						</div>
					</div>
					&nbsp;
				</div>
			</div>
		</div>
		<?php echo $this->element('Croogo/Croogo.admin/footer'); ?>
		<?php
		echo $this->Blocks->get('scriptBottom');
//		echo $this->Js->writeBuffer();
		?>
	</body>
</html>

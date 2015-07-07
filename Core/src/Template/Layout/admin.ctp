<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title><?php echo $this->fetch('title'); ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
		<?php

		echo $this->Html->css(array(
			'Croogo/Core.croogo-bootstrap',
			'Croogo/Core.croogo-bootstrap-responsive',
			'Croogo/Core.thickbox',
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'Croogo/Core.html5',
			'Croogo/Core.jquery/jquery.min',
			'Croogo/Core.jquery/jquery-ui.min',
			'Croogo/Core.jquery/jquery.slug',
			'Croogo/Core.jquery/jquery.cookie',
			'Croogo/Core.jquery/jquery.hoverIntent.minified',
			'Croogo/Core.jquery/superfish',
			'Croogo/Core.jquery/supersubs',
			'Croogo/Core.jquery/jquery.tipsy',
			'Croogo/Core.jquery/jquery.elastic-1.6.1.js',
			'Croogo/Core.jquery/thickbox-compressed',
			'Croogo/Core.underscore-min',
			'Croogo/Core.admin',
			'Croogo/Core.sidebar',
			'Croogo/Core.choose',
			'Croogo/Core.typeahead_autocomplete',
			'Croogo/Core.croogo-bootstrap.js',
		));

		echo $this->fetch('script');
		echo $this->fetch('css');

		?>
	</head>
	<body>
		<div id="wrap">
			<?php echo $this->element('Croogo/Core.admin/header'); ?>
			<?php echo $this->element('Croogo/Core.admin/navigation'); ?>
			<div id="push"></div>
			<div id="content-container" class="container-fluid">
				<div class="row-fluid">
					<div id="content" class="clearfix">
						<?php echo $this->element('Croogo/Core.admin/breadcrumb'); ?>
						<div id="inner-content" class="span12">
							<?php echo $this->Flash->render(); ?>
							<?php echo $this->fetch('content'); ?>
						</div>
					</div>
					&nbsp;
				</div>
			</div>
		</div>
		<?php echo $this->element('Croogo/Core.admin/footer'); ?>
		<?php
		echo $this->Blocks->get('scriptBottom');
//		echo $this->Js->writeBuffer();
		?>
	</body>
</html>

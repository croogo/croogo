<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title><?php echo $title_for_layout; ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
		<?php

		echo $this->Html->css(array(
			'/croogo/css/thickbox',
			'/croogo/css/admin',
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'/croogo/js/jquery/jquery.min',
			'/croogo/js/jquery/jquery-ui.min',
			'/croogo/js/jquery/jquery.slug',
			'/croogo/js/jquery/jquery.cookie',
			'/croogo/js/jquery/jquery.hoverIntent.minified',
			'/croogo/js/jquery/superfish',
			'/croogo/js/jquery/supersubs',
			'/croogo/js/jquery/jquery.tipsy',
			'/croogo/js/jquery/jquery.elastic-1.6.1.js',
			'/croogo/js/jquery/thickbox-compressed',
			'/croogo/js/underscore-min',
			'/croogo/js/admin',
			'/croogo/js/sidebar',
			'/croogo/js/choose',
			'/croogo/js/typeahead_autocomplete',
			'/croogo/js/croogo-bootstrap.js',
		));

		echo $this->fetch('script');
		echo $this->fetch('css');

		?>
	</head>
	<body>
		<div id="wrap">
			<?php echo $this->element('admin/header'); ?>
			<?php echo $this->element('admin/navigation'); ?>
			<div id="push"></div>
			<div id="content-container" class="<?php echo $this->Theme->getCssClass('container'); ?>">
				<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
					<div id="content" class="clearfix">
						<?php echo $this->element('admin/breadcrumb'); ?>
						<div id="inner-content" class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
							<?php echo $this->Layout->sessionFlash(); ?>
							<?php echo $this->fetch('content'); ?>
						</div>
					</div>
					&nbsp;
				</div>
			</div>
		</div>
		<?php echo $this->element('admin/footer'); ?>
		<?php
		echo $this->element('admin/initializers');
		echo $this->Blocks->get('scriptBottom');
		echo $this->Js->writeBuffer();
		?>
	</body>
</html>
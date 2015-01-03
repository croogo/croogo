<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title><?php echo $title_for_layout; ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
		<?php

		echo $this->Html->css(array(
			'/croogo/css/jquery-ui.min',
			'/croogo/css/admin.min',
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'/croogo/js/jquery/jquery-1.11.2.min.js',
//			'http://code.jquery.com/jquery-migrate-1.2.1.js', // To debug some deprecated jQuery files
			'/croogo/js/jquery/jquery-ui-1.11.2.min',
			'/croogo/js/jquery/jquery.slug.min',
			'/croogo/js/jquery/jquery.cookie.min',
			'/croogo/js/jquery/jquery.hoverIntent-1.8.0.min',
			'/croogo/js/jquery/jquery.superfish-1.7.5.min',
			'/croogo/js/jquery/jquery.elastic-1.6.11.min.js',
			'/croogo/js/jquery/jquery.thickbox-3.1.min',
			'/croogo/js/bootstrap.min.js',
			'/croogo/js/bootstrap3-typeahead-3.1.0.min',
			'/croogo/js/underscore-min',
			'/croogo/js/admin',
			'/croogo/js/sidebar',
			'/croogo/js/choose',
			'/croogo/js/typeahead_autocomplete',
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
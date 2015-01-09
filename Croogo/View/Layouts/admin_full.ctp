<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo $title_for_layout; ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
	<?php
		echo $this->Html->css(array(
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
		));
		echo $this->Blocks->get('css');
		echo $this->Blocks->get('script');
	?>
</head>

<body>

	<div id="wrap" class="full">
		<?php echo $this->element('admin/header'); ?>
		<div id="push"></div>
		<div id="content-container" class="<?php echo $this->Theme->getCssClass('container'); ?>">
			<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
				<div id="content" class="clearfix">
					<div id="inner-content" class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
					<?php
						echo $this->Layout->sessionFlash();
						echo $content_for_layout;
					?>
					</div>
					&nbsp;
				</div>
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
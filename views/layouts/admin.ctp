<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title_for_layout; ?> - <?php __('Croogo'); ?></title>
	<?php
		echo $this->Html->css(array(
			'reset',
			'960',
			'/ui-themes/smoothness/jquery-ui.css',
			'admin',
			'thickbox',
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'jquery/jquery.min',
			'jquery/jquery-ui.min',
			'jquery/jquery.slug',
			'jquery/jquery.uuid',
			'jquery/jquery.cookie',
			'jquery/jquery.hoverIntent.minified',
			'jquery/superfish',
			'jquery/supersubs',
			'jquery/jquery.tipsy',
			'jquery/jquery.elastic-1.6.1.js',
			'jquery/thickbox-compressed',
			'admin',
		));
		echo $scripts_for_layout;
	?>
</head>

<body>

	<div id="wrapper">
		<?php echo $this->element('admin/header'); ?>

		<div id="nav-container">
			<div class="container_16">
				<?php echo $this->element("admin/navigation"); ?>
			</div>
		</div>

		<div id="main" class="container_16">
			<div class="grid_16">
				<div id="content">
					<?php
						echo $this->Layout->sessionFlash();
						echo $content_for_layout;
					?>
				</div>
			</div>
			<div class="clear">&nbsp;</div>
		</div>
		
		<div class="push"></div>
	</div>

	<?php echo $this->element('admin/footer'); ?>

	</body>
</html>
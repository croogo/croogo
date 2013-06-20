<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo $title_for_layout; ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
	<?php
		echo $this->Html->css(array(
			'/croogo/css/croogo-bootstrap',
			'/croogo/css/croogo-bootstrap-responsive',
		));
		echo $this->element('styles', array(), array('plugin' => 'install'));
		echo $this->Html->script(array(
			'/croogo/js/jquery/jquery.min',
			'/croogo/js/croogo-bootstrap',
		));
		echo $scripts_for_layout;
	?>
</head>

<body>

	<div id="wrap" class="install">
		<header class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container-fluid">
					<span class="brand"><?php echo __d('croogo', 'Install Croogo'); ?></span>
				</div>
			</div>
		</header>

		<div id="main" class="container-fluid">
			<div class="row-fluid">
				<div id="install" class="span12">
				<?php
					echo $this->Layout->sessionFlash();
					echo $content_for_layout;
				?>
				</div>
			</div>
		</div>

	</div>

	<?php echo $this->element('admin/footer'); ?>
	<?php
	$script = <<<EOF
$('[rel=tooltip],input[data-title]').tooltip();
EOF;
	$this->Js->buffer($script);

	echo $this->Js->writeBuffer();
	?>
	</body>
</html>
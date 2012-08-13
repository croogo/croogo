<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title_for_layout; ?> - <?php __('Croogo'); ?></title>
	<?php
		echo $this->Html->css(array(
			/*'reset',
			'960',
			'admin',*/

			'bootstrap/bootstrap',

	        'admin/login',
	        'admin/theme-default'
		));
		echo $scripts_for_layout;
	?>
</head>

<body>

	<div id="croogo-login-wrapper">
        <div id="croogo-login">
        	<?php
				echo $content_for_layout;
				echo $this->Layout->sessionFlash();
			?>
        </div>
        <?php echo $this->element('admin/footer'); ?>
    </div>

	

	</body>
</html>
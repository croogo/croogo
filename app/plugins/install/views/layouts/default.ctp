<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<?php echo $html->charset(); ?>
	<title>Install Croogo: <?php echo $this->pageTitle; ?></title>
	<?php
		echo $html->meta('icon');
		echo $html->css('cake.generic');
		echo $scripts_for_layout;
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1><?php __('Install Croogo'); ?></h1>
		</div>
		<div id="content">
			<?php 
                $session->flash();
                echo $content_for_layout;
            ?>
		</div>
		<div id="footer">
			<?php echo $html->link(
					$html->image('cake.power.gif', array('alt'=> __("CakePHP: the rapid development php framework", true), 'border'=>"0")),
					'http://www.cakephp.org/',
					array('target'=>'_blank'), null, false
				);
			?>
		</div>
	</div>
</body>
</html>
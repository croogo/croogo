<?php
/**
 * Default Error Layout for Croogo CMS
 *
 * @author Fahad Ibnay Heylaal <contact@fahad19.com>
 * @link http://www.croogo.org
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title_for_layout; ?> &raquo; <?php echo Configure::read('Site.title'); ?></title>
	<?php
		echo $this->Html->css(array(
			'/croogo/css/reset',
			'/croogo/css/960',
			'/croogo/css/theme',
		));
	?>
</head>
<body>
	<div id="wrapper">
		<div id="header" class="container_16">
			<div class="grid_16">
				<h1 class="site-title"><?php echo $this->Html->link(Configure::read('Site.title'), '/'); ?></h1>
				<span class="site-tagline"><?php echo Configure::read('Site.tagline'); ?></span>
			</div>
			<div class="clear"></div>
		</div>

		<div id="nav"></div>

		<div id="main" class="container_16">
			<div id="content" class="grid_16">
			<?php
				echo $this->Layout->sessionFlash();
				echo $content_for_layout;
			?>
			</div>

			<div class="clear"></div>
		</div>

		<div id="footer">
			<div class="container_16">
				<div class="grid_8 left">
					Powered by <a href="http://www.croogo.org">Croogo</a>.
				</div>
				<div class="grid_8 right">
					<a href="http://www.cakephp.org"><?php echo $this->Html->image('/img/cake.power.gif'); ?></a>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	</body>
</html>
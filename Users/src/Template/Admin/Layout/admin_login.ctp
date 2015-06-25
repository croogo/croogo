<?php
use Cake\Core\Configure;
?>
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
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'Croogo/Croogo.html5',
		));

		echo $this->fetch('script');
		echo $this->fetch('css');
		?>
	</head>
	<body class="admin-login">
		<div id="wrap">

			<header class="navbar navbar-inverse navbar-fixed-top">
				<div class="navbar-inner">
					<div class="<?php echo $this->Layout->cssClass('container'); ?>">
						<?php echo $this->Html->link(
							__d('croogo', 'Back to') . ' ' . Configure::read('Site.title'),
							'/',
							array('class' => 'brand')
						);
						?>
					</div>
				</div>
			</header>

			<div id="push"></div>
			<div id="content-container" class="<?php echo $this->Layout->cssClass('container'); ?>">
				<div class="<?php echo $this->Layout->cssClass('row'); ?>">
					<div id="admin-login">
					<?php
					echo $this->Flash->render('auth');
					echo $this->fetch('content');
					?>
					</div>
				</div>
			</div>

		</div>
		<?php echo $this->element('Croogo/Croogo.admin/footer'); ?>
	</body>
</html>

<footer class="navbar-inverse">
	<div class="navbar-inner">

	<div class="footer-content">
	<?php
		$link = $this->Html->link(
			__('Croogo %s', strval(Configure::read('Croogo.version'))),
			'http://www.croogo.org'
		);
	?>
	Powered by <?php echo $link; ?>
	<?php echo $this->Html->image('http://assets.croogo.org/powered_by.png'); ?>
	</div>

	</div>
</footer>
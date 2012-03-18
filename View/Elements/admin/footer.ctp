<div id="footer">
	<?php $product = __('Croogo %s', strval(Configure::read('Croogo.version'))); ?>
	Powered by <?php echo $this->Html->link($product, 'http://www.croogo.org'); ?>
	<?php echo $this->Html->image('http://assets.croogo.org/powered_by.png'); ?>
</div>
<div class="nodes promoted">
<<<<<<< HEAD
	<?php
		if (count($nodes) == 0) {
			__('No items found.');
		} else {
			foreach ($this->params['named'] AS $nn => $nv) {
				$this->Paginator->options['url'][$nn] = $nv;
			}
		}
	?>
=======
	<?php
		if (count($nodes) == 0) {
			__('No items found.');
		} else {
			foreach ($this->params['named'] AS $nn => $nv) {
				$paginator->options['url'][$nn] = $nv;
			}
		}
	?>
>>>>>>> 1.3-whitespace

	<?php 
		foreach ($nodes AS $node) {
			$this->Layout->setNode($node);
	?>
	<div id="node-<?php echo $this->Layout->node('id'); ?>" class="node node-type-<?php echo $this->Layout->node('type'); ?>">
		<h2><?php echo $this->Html->link($this->Layout->node('title'), $this->Layout->node('url')); ?></h2>
		<?php
			echo $this->Layout->nodeInfo();
			echo $this->Layout->nodeBody();
			echo $this->Layout->nodeMoreInfo();
		?>
	</div>
	<?php 
		}
	?>

<<<<<<< HEAD
	<div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
=======
	<div class="paging"><?php echo $paginator->numbers(); ?></div>
>>>>>>> 1.3-whitespace
</div>
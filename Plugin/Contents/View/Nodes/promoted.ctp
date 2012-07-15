<div class="nodes promoted">
	<?php
		if (count($nodes) == 0) {
			__('No items found.');
		} else {
			foreach ($this->params['named'] AS $nn => $nv) {
				$this->Paginator->options['url'][$nn] = $nv;
			}
		}
	?>

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

	<div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
</div>
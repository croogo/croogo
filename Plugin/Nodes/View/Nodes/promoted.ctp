<div class="nodes promoted">
	<?php
		if (count($nodes) == 0) {
			__('No items found.');
		} else {
			foreach ($this->params['named'] as $nn => $nv) {
				$this->Paginator->options['url'][$nn] = $nv;
			}
		}
	?>

	<?php
		foreach ($nodes as $node) {
			$this->Nodes->set($node);
	?>
	<div id="node-<?php echo $this->Nodes->field('id'); ?>" class="node node-type-<?php echo $this->Nodes->field('type'); ?>">
		<h2><?php echo $this->Html->link($this->Nodes->field('title'), $this->Nodes->field('url')); ?></h2>
		<?php
			echo $this->Nodes->info();
			echo $this->Nodes->body();
			echo $this->Nodes->moreInfo();
		?>
	</div>
	<?php
		}
	?>

	<div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
</div>
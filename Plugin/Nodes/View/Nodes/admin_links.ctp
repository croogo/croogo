<?php

echo $this->Html->css('admin');
echo $this->Html->script('jquery/jquery.min');

?>
<script>
$(function() {
	$('#nodes-for-links a').click(function() {
		parent.$('#LinkLink').val($(this).attr('rel'));
		parent.tb_remove();
		return false;
	});
});
</script>
<div class="nodes">
	<div>
		<?php
			if (isset($this->params['named'])) {
				foreach ($this->params['named'] as $nn => $nv) {
					$this->Paginator->options['url'][] = $nn . ':' . $nv;
				}
			}

			echo __('Sort by:');
			echo ' ' . $this->Paginator->sort('id');
			echo ', ' . $this->Paginator->sort('title');
			echo ', ' . $this->Paginator->sort('created');
		?>
	</div>

	<hr />

	<ul id="nodes-for-links">
	<?php foreach ($nodes as $node) { ?>
		<li>
		<?php
			echo $this->Html->link($node['Node']['title'], array(
				'admin' => false,
				'plugin' => 'nodes',
				'controller' => 'nodes',
				'action' => 'view',
				'type' => $node['Node']['type'],
				'slug' => $node['Node']['slug'],
			), array(
				'rel' => sprintf(
					'plugin:%s/controller:%s/action:%s/type:%s/slug:%s',
					'nodes',
					'nodes',
					'view',
					$node['Node']['type'],
					$node['Node']['slug']
					),
			));
		?>
		</li>
	<?php } ?>
	</ul>
	<div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
</div>
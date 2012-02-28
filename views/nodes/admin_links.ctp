<?php
	echo $this->Html->css('admin');
	echo $this->Html->script('jquery/jquery.min');
?>
<script type="text/javascript">
	$(document).ready(function() {
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
				foreach ($this->params['named'] AS $nn => $nv) {
					$paginator->options['url'][] = $nn . ':' . $nv;
				}
			}

			__('Sort by:');
			echo ' ' . $paginator->sort('id');
			echo ', ' . $paginator->sort('title');
			echo ', ' . $paginator->sort('created');
		?>
	</div>
	
	<hr />

	<ul id="nodes-for-links">
	<?php foreach ($nodes AS $node) { ?>
		<li>
		<?php
			echo $this->Html->link($node['Node']['title'], array(
				'admin' => false,
				'controller' => 'nodes',
				'action' => 'view',
				'type' => $node['Node']['type'],
				'slug' => $node['Node']['slug'],
			), array(
				'rel' => 'controller:nodes/action:view/type:' . $node['Node']['type'] . '/slug:' . $node['Node']['slug'],
			));
		?>
		</li>
	<?php } ?>
	</ul>
	<div class="paging"><?php echo $paginator->numbers(); ?></div>
</div>
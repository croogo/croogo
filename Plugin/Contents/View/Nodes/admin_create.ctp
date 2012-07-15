<div class="nodes create">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="types">
		<?php foreach ($types AS $type) { ?>
		<div class="type">
			<h3><?php echo $this->Html->link($type['Type']['title'], array('action' => 'add', $type['Type']['alias'])); ?></h3>
			<p><?php echo $type['Type']['description']; ?></p>
		</div>
		<?php } ?>
	</div>
</div>
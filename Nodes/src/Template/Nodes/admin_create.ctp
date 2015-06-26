<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>
<?php

$this->Html->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Content'), array('controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Create'), '/' . $this->request->url);

?>
<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
	<div class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
		<div class="box">
			<div class="box-content">
				<?php foreach ($types as $type): ?>
					<?php
						if (!empty($type['Type']['plugin'])):
							continue;
						endif;
					?>
					<div class="type">
						<h3><?php echo $this->Html->link($type['Type']['title'], array('action' => 'add', $type['Type']['alias'])); ?></h3>
						<p><?php echo $type['Type']['description']; ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

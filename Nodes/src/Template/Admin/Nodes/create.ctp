<?php
$this->assign('title', __d('croogo', 'Create content'));
?>`
<h2 class="hidden-desktop"><?php echo $this->fetch('title'); ?></h2>
<?php

$this->Html->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('action' => 'index'))
	->addCrumb(__d('croogo', 'Create'), '/' . $this->request->url);

?>
<div class="row-fluid">
	<div class="span12">
		<div class="box">
			<div class="box-content">
				<?php foreach ($types as $type): ?>
					<?php
						if (!empty($type->plugin)):
							continue;
						endif;
					?>
					<div class="type">
						<h3><?php echo $this->Html->link($type->title, array('action' => 'add', $type->alias)); ?></h3>
						<p><?php echo $type->description; ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

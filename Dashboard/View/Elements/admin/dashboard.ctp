<div class='box box-<?php echo $alias; ?>'>
	<div class='box-title'>
		<i class='icon-<?php echo $dashboard['icon'] ?>'></i>
		<?php echo $dashboard['title'] ?>
	</div>
	<div class='box-content'>
		<?php echo $this->element($dashboard['element'], array(), array('cache' => $dashboard['cache']));?>
	</div>
</div>
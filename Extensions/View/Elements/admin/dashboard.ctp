<div class='box <?php echo $class?>'>
	<div class='box-title'>
		<i class='icon-<?php echo $dashboard['icon'] ?>'></i>
		<?php echo $dashboard['title'] ?>
	</div>
	<div class='box-content %s'>
		<?php echo $this->element($dashboard['element'], array(), array('cache' => $dashboard['cache']));?>
	</div>
</div>
<div class="box box-<?php echo $alias; ?>" data-alias="<?php echo $alias ?>">
	<div class="box-title">
		<i class="icon-<?php echo $dashboard['icon'] ?>"></i>
		<?php echo $dashboard['title'] ?>
		<div class="toggle-icon pull-right">
			<?php echo($dashboard['collapsed'] ? '<i class="icon-plus"></i>' : '<i class="icon-minus"></i>') ?>
		</div>
	</div>
	<div class="box-content" <?php echo ($dashboard['collapsed'] ? 'style="display:none;"' : '')?>>
		<?php echo $this->element($dashboard['element'], array(), array('cache' => $dashboard['cache']));?>
	</div>
</div>
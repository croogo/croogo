<div class="box box-<?php echo $alias; ?> dashboard-box" id="<?php echo $alias ?>">
	<div class="box-title">
		<i class="icon-move move-handle"></i>
		<?php echo $dashboard['title'] ?>
		<div class="toggle-icon pull-right">
			<?php echo($dashboard['collapsed'] ? '<i class="icon-plus"></i>' : '<i class="icon-minus"></i>') ?>
		</div>
	</div>
	<div class="box-content" <?php echo ($dashboard['collapsed'] ? 'style="display:none;"' : '')?>>
		<?php echo $this->element($dashboard['element'], compact('alias', 'dashboard'), array('cache' => $dashboard['cache']));?>
	</div>
</div>
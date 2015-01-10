<?php
$title = isset($title) ? $title : null;
if (empty($id)) {
	$id = 'modal';
}
if (empty($class)) {
	$class = 'modal fade';
} else {
	$class .= ' modal fade';
}
?>
<div id="<?php echo $id; ?>" class="<?php echo trim($class); ?>">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo __d('croogo', 'Close');?>">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"><?php echo $title; ?></h4>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __d('croogo', 'Close'); ?></button>
			</div>
		</div>
	</div>
</div>

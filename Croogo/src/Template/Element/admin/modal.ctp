<?php
$title = isset($title) ? $title : null;
if (empty($id)) {
	$id = 'modal';
}
if (empty($class)) {
	$class = 'modal hide';
} else {
	$class .= ' modal hide';
}
?>
<div id="<?php echo $id; ?>" class="<?php echo trim($class); ?>">
	<div class="modal-header">
		 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		 <h3><?php echo $title; ?></h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __d('croogo', 'Close'); ?></button>
	</div>
</div>

<?php
$this->Html->script('Croogo/Core.modal', ['block' => true]);
$title = isset($title) ? $title : null;
if (empty($id)) {
    $id = 'modal';
}
if (empty($class)) {
    $class = 'modal fade';
} else {
    $class .= ' modal fade';
}
if (!isset($modalSize)) {
    $modalSize = '';
}
if (!isset($body)) {
    $body = '';
}
?>
<div id="<?php echo $id; ?>" class="<?php echo trim($class); ?>">
    <div class="modal-dialog <?= trim($modalSize) ?>">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title"><?=$title; ?></h3>
            </div>
            <div class="modal-body">
                <?= $body ?>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">
                    <?= __d('croogo', 'Close'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

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
if (!isset($modalSize)) {
    $modalSize = '';
}
if (!isset($body)) {
    $body = '';
}
if (!isset($footer)) {
    $footer = '';
}
?>
<div id="<?= $id ?>" class="<?= trim($class) ?>">
    <div class="modal-dialog <?= trim($modalSize) ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $title ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $body ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">
                    <?= __d('croogo', 'Close') ?>
                </button>
                <?= $footer ?>
            </div>
        </div>
    </div>
</div>

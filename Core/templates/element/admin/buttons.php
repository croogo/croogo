<?php
/**
 * @var \App\View\AppView $this
 */

$iconSet = $this->Theme->settings('iconDefaults.iconSet');
$cancelUrl = isset($cancelUrl) ? $cancelUrl : ['action' => 'index'];
$saveText = isset($saveText) ? $saveText : __d('croogo', 'Save');
$applyText = isset($applyText) ? $applyText : __d('croogo', 'Apply');
$cancelText = isset($cancelText) ? $cancelText : __d('croogo', 'Cancel');

$saveLabel = $this->Html->icon('save') . $saveText;
$applyLabel = $this->Html->icon('bolt') . $applyText;
$cancelLabel = $this->Html->icon('times') . $cancelText;

?>
<div class="clearfix">
    <div class="card-buttons d-flex justify-content-center">
    <?php
        echo $this->Form->button($saveLabel, [
            'class' => 'btn-outline-success',
            'escapeTitle' => false,
        ]);
    if ($applyText) :
        echo $this->Form->button($applyLabel, [
            'class' => 'btn-outline-primary',
            'name' => '_apply',
            'escapeTitle' => false,
        ]);
    endif;
        echo $this->Html->link($cancelLabel, $cancelUrl, [
            'escapeTitle' => false,
            'class' => 'cancel btn btn-outline-danger'
        ]);
        ?>
    </div>
</div>

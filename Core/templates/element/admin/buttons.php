<?php
/**
 * @var \Croogo\Core\View\CroogoView $this
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
        echo $this->Html->link($cancelLabel, $cancelUrl, [
            'escapeTitle' => false,
            'class' => 'cancel btn btn-outline-danger'
        ]);
    if ($applyText) :
        // Cake 5: Form->button escapuje tytul przez escapeTitle (nie escape). Etykieta
        // jest HTML (ikona), wiec bez tego renderowaloby doslownie <i ...>Zastosuj.
        echo $this->Form->button($applyLabel, ['class' => 'btn-outline-primary',
            'name' => '_apply',
            'escapeTitle' => false,
        ]);
    endif;

        echo $this->Form->button($saveLabel, ['class' => 'btn-outline-success', 'escapeTitle' => false]);
        ?>
    </div>
</div>

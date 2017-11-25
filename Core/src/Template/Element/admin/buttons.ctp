<?php
/**
 * @var \Croogo\Core\View\CroogoView $this
 */

$cancelUrl = isset($cancelUrl) ? $cancelUrl : ['action' => 'index'];
$saveText = isset($saveText) ? $saveText : '<i class="fa fa-save"></i> ' . __d('croogo', 'Save');
$defaultApplyText = __d('croogo', 'Apply');
if (isset($applyText)):
    if ($applyText !== false):
        $applyText = $defaultApplyText;
    endif;
else:
    $applyText = '<i class="fa fa-bolt"></i> ' . $defaultApplyText;
endif;


?>
<div class="clearfix">
    <div class="card-buttons d-flex justify-content-center">
    <?php
        echo $this->Form->button($saveText, ['class' => 'btn-outline-success']);
        if ($applyText):
            echo $this->Form->button($applyText, ['class' => 'btn-outline-primary',
                'name' => '_apply',
            ]);
        endif;
        echo $this->Html->link(__d('croogo', 'Cancel'), $cancelUrl, [
            'class' => 'cancel btn btn-outline-danger'
        ]);
    ?>
    </div>
</div>

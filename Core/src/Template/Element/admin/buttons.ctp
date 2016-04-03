<?php
/**
 * @var \Croogo\Core\View\CroogoView $this
 */

$cancelUrl = isset($cancelUrl) ? $cancelUrl : ['action' => 'index'];
$saveText = isset($saveText) ? $saveText : __d('croogo', 'Save %s', lcfirst($type));

echo '<div class="clearfix"><div class="pull-left">';
echo $this->Form->button($saveText,
    ['button' => 'success', 'class' => 'btn-success-outline']);
echo '</div><div class="pull-right">';
echo $this->Html->link(__d('croogo', 'Cancel'), $cancelUrl, ['class' => 'cancel btn btn-danger']);
echo '</div></div>';

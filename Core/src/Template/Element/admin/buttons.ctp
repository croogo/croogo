<?php
/**
 * @var \Croogo\Core\View\CroogoView $this
 */

$cancelUrl = $cancelUrl ?: ['action' => 'index'];

echo '<div class="clearfix"><div class="pull-left">';
echo $this->Form->button(__d('croogo', 'Save %s', lcfirst($type)),
    ['button' => 'success', 'class' => 'btn-success-outline']);
echo '</div><div class="pull-right">';
echo $this->Html->link(__d('croogo', 'Cancel'), $cancelUrl, ['class' => 'cancel btn btn-danger']);
echo '</div></div>';

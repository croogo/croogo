<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="node-body my-2">
    <?= $this->Layout->filter($this->Nodes->field('body')) ?>
</div>

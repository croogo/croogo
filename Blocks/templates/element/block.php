<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\Blocks\Model\Entity\Block $block
 */
$this->set(compact('block'));
$class = 'block block-' . $block->alias;
if ($block->class != null) {
    $class .= ' ' . $block->class;
}
?>
<div id="block-<?= $block->id ?>" class="<?= $class ?>">
<?php if ($block->show_title == 1): ?>
    <h3><?= $block->title ?></h3>
<?php endif ?>
    <div class="block-body">
<?php
    echo $this->Layout->filter($block->body, array(
        'model' => 'Block', 'id' => $block->id
    ));
?>
    </div>
</div>

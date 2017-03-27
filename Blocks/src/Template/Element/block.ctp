<?php
$this->set(compact('block'));
$class = 'block block-' . $block->alias;
if ($block->class != null) {
    $class .= ' ' . $block->class;
}
?>
<div id="block-<?php echo $block->id; ?>" class="<?php echo $class; ?>">
<?php if ($block->show_title == 1): ?>
    <h3><?php echo $block->title; ?></h3>
<?php endif; ?>
    <div class="block-body">
<?php
    echo $this->Layout->filter($block->body, array(
        'model' => 'Block', 'id' => $block->id
    ));
?>
    </div>
</div>

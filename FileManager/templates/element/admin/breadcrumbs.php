<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $path
 */
?>
<ul class="breadcrumb">
    <?php $breadcrumb = $this->FileManager->breadcrumb($path) ?>
    <?php foreach ($breadcrumb as $pathname => $p) : ?>
        <li class="breadcrumb-item"><?= $this->FileManager->linkDirectory($pathname, $p) ?></li>
    <?php endforeach ?>
</ul>

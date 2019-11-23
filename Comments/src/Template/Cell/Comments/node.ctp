<?php
$commentCount = count($node->comments);
?>
<?php if ($commentCount > 0): ?>
<div class="comments">
    <h5><?= __dn('croogo', 'Comment', 'Comments', $commentCount) ?></h5>
    <?php foreach ($node->comments as $comment) : ?>
        <?= $this->element('Croogo/Comments.comment', ['comment' => $comment, 'level' => 1]) ?>
    <?php endforeach; ?>
</div>
<?php endif ?>

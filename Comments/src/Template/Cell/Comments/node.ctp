<?php
$commentCount = count($entity->comments);
?>
<?php if ($commentCount > 0): ?>
<div class="comments">
    <h5><?= __dn('croogo', 'Comment', 'Comments', $commentCount) ?></h5>
    <?php foreach ($entity->comments as $comment) : ?>
        <?= $this->element('Croogo/Comments.comment', ['entity' => $entity, 'comment' => $comment, 'level' => 1]) ?>
    <?php endforeach; ?>
</div>
<?php endif ?>

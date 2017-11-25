<div class="comments">
    <h3><?= (__dn('croogo', 'Comment', 'Comments', count($node->comments))); ?></h3>
    <?php foreach ($node->comments as $comment): ?>
        <?= $this->element('Croogo/Comments.comment', ['comment' => $comment, 'level' => 1]); ?>
    <?php endforeach; ?>
</div>

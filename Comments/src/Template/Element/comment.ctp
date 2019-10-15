<?php

use Cake\Core\Configure;

?>
<div id="comment-<?= $comment->id; ?>" class="comment-level-<?= $level ?> comment<?php if ($node['Node']['user_id'] == $comment->user_id) {
    echo ' author';
                 } ?>">
    <div class="comment-info">
        <span class="avatar"><?= $this->Html->image('http://www.gravatar.com/avatar/' . md5(strtolower($comment->email)) . '?s=32'); ?></span>
        <span class="name">
            <?php if ($comment->website) : ?>
                <?= $this->Html->link($comment->name, $comment->website, ['target' => '_blank']); ?>
            <?php else : ?>
                <?= h($comment->name); ?>
            <?php endif; ?>
        </span>
        <span class="date"><?= h(__d('croogo', 'said on %s', $this->Time->i18nFormat($comment->created))); ?></span>
    </div>
    <div class="comment-body"><?= $this->Text->autoParagraph($this->Text->autoLink($comment->body)); ?></div>

    <?php if (!isset($hideReplyButton)): ?>
    <div class="comment-reply">
        <?php if ($level <= Configure::read('Comment.level')) : ?>
            <?= $this->Html->link(__d('croogo', 'Reply'), [
                'plugin' => 'Croogo/Comments',
                'controller' => 'Comments',
                'action' => 'add',
                urlencode($comment->model),
                $comment->foreign_key,
                $comment->id,
            ), [
                'class' => 'btn btn-sm btn-outline-secondary',
            ]); ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php foreach ((array)$comment->children as $childComment) : ?>
        <?= $this->element('Croogo/Comments.comment', ['comment' => $childComment, 'level' => $level + 1]); ?>
    <?php endforeach; ?>
</div>

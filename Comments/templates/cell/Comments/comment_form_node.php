<?php
$captcha = (isset($captcha)) ? $captcha : false;
$entity = isset($entity) ? $entity : null;
?>
<div class="comment-form">
    <h6><?= h(__d('croogo', 'Add new comment')); ?></h6>
    <?php if ($this->getRequest()->getParam('controller') == 'Comments') : ?>
        <p class="back">
            <?= $this->Html->link(__d('croogo', 'Go back to original post: %s', $title), $url->getUrl()); ?>
        </p>
    <?php endif; ?>

    <?php
        if (isset($parentComment)):
        echo $this->element('Croogo/Comments.comment', [
            'comment' => $parentComment,
            'entity' => $entity,
            'level' => 1,
            'hideReplyButton' => true,
        ]);
    endif;
    ?>
    <?= $this->Form->create($comment, ['url' => $formUrl]); ?>
    <?php if (!$loggedInUser) : ?>
        <?= $this->Form->input('name', ['label' => __d('croogo', 'Name')]); ?>
        <?= $this->Form->input('email', ['label' => __d('croogo', 'Email')]); ?>
        <?= $this->Form->input('website', ['label' => __d('croogo', 'Website')]); ?>
    <?php endif; ?>
    <?= $this->Form->input('body', ['label' => false]); ?>
    <?php if ($captcha) : ?>
        <?= $this->Recaptcha->display(); ?>
    <?php endif; ?>
    <?= $this->Form->submit(__d('croogo', 'Post comment')); ?>
    <?= $this->Form->end(); ?>
</div>

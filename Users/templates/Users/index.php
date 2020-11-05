<?php
/**
 * @var \App\View\AppView $this
 * @var string $title_for_layout
 */
?>
<div class="users index">
    <h2><?= $title_for_layout ?></h2>

    <p><?= __d('croogo', 'You are currently logged in as:') . ' ' . $this->getRequest()->getSession()->read('Auth.User.username') ?></p>
</div>

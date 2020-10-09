<div class="users index">
    <h2><?= $title_for_layout ?></h2>

    <p><?= __d('croogo', 'You are currently logged in as:') . ' ' . $this->getRequest()->getSession()->read('Auth.User.username') ?></p>
</div>

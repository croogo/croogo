<div class="users index">
    <h2><?php echo $this->pageTitle; ?></h2>

    <p><?php echo __('You are currently logged in as:') . ' ' . $session->read('Auth.User.username'); ?></p>
</div>
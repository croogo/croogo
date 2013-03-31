<div class="users index">
	<h2><?php echo $title_for_layout; ?></h2>

	<p><?php echo __d('croogo', 'You are currently logged in as:') . ' ' . $this->Session->read('Auth.User.username'); ?></p>
</div>
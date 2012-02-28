<?php $title_for_layout = __('Page not found'); ?>
<h2><?php echo __('Security Error'); ?></h2>
<p class="error">
	<?php echo __('The requested address was not found on this server.'); ?>
</p>
<?php if (Configure::read('debug') > 0): ?>
<p class="notice">
	Request blackholed due to "<?php echo $type; ?>" violation.
</p>
<?php endif; ?>
<?php Configure::write('debug', 0); ?>
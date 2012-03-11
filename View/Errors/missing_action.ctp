<?php $title_for_layout = __('Page not found'); ?>
<h2><?php echo __('Error'); ?></h2>
<p class="error">
	<?php echo __('The requested address was not found on this server.'); ?>
	<!-- action -->
</p>
<?php Configure::write('debug', 0); ?>
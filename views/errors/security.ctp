<?php $title_for_layout = __('Page not found', true); ?>
<h2><?php __('Security Error'); ?></h2>
<p class="error">
    <?php __('The requested address was not found on this server.'); ?>
</p>
<?php Configure::write('debug', 0); ?>
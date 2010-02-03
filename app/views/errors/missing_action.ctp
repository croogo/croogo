<?php $title_for_layout = __('Page not found', true); ?>
<h2><?php __('Error'); ?></h2>
<p class="error">
    <?php __('The requested address was not found on this server.'); ?>
    <!-- action -->
</p>
<?php Configure::write('debug', 0); ?>
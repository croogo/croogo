<div class="install index">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php
		$check = true;

		// tmp is writable
		if (is_writable(TMP)) {
			echo '<p class="success">' . __('Your tmp directory is writable.') . '</p>';
		} else {
			$check = false;
			echo '<p class="error">' . __('Your tmp directory is NOT writable.') . '</p>';
		}

		// config is writable
		if (is_writable(APP.'Config')) {
			echo '<p class="success">' . __('Your config directory is writable.') . '</p>';
		} else {
			$check = false;
			echo '<p class="error">' . __('Your config directory is NOT writable.') . '</p>';
		}

		// php version
		if (phpversion() > 5) {
			echo '<p class="success">' . sprintf(__('PHP version %s > 5'), phpversion()) . '</p>';
		} else {
			$check = false;
			echo '<p class="error">' . sprintf(__('PHP version %s < 5'), phpversion()) . '</p>';
		}

		if ($check) {
			echo '<p>' . $this->Html->link('Click here to begin installation', array('action' => 'database')) . '</p>';
		} else {
			echo '<p>' . __('Installation cannot continue as minimum requirements are not met.') . '</p>';
		}
	?>
</div>
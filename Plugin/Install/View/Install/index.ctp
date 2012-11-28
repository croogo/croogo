<div class="install">
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
		if (is_writable(APP . 'Config')) {
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

		// php version
		$minCakeVersion = '2.2.1';
		$cakeVersion = Configure::version();
		if (version_compare($cakeVersion, $minCakeVersion, '>=')) {
			echo '<p class="success">' . __('CakePhp version %s >= %s', $cakeVersion, $minCakeVersion) . '</p>';
		} else {
			$check = false;
			echo '<p class="error">' . __('CakePHP version %s < %s', $cakeVersion, $minCakeVersion) . '</p>';
		}

?>
</div>
<?php
if ($check) {
	$out = $this->Html->link(__('Install'), array(
		'action' => 'database',
	), array(
		'button' => 'success',
		'tooltip' => array(
			'data-title' => __('Click here to begin installation'),
			'data-placement' => 'left',
		),
	));
} else {
	$out = '<p>' . __('Installation cannot continue as minimum requirements are not met.') . '</p>';
}
echo $this->Html->div('form-actions', $out);
?>
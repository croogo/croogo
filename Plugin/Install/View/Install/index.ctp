<div class="install">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php
		$check = true;

		// tmp is writable
		if (is_writable(TMP)) {
			echo '<p class="success">' . __d('croogo', 'Your tmp directory is writable.') . '</p>';
		} else {
			$check = false;
			echo '<p class="error">' . __d('croogo', 'Your tmp directory is NOT writable.') . '</p>';
		}

		// config is writable
		if (is_writable(APP . 'Config')) {
			echo '<p class="success">' . __d('croogo', 'Your config directory is writable.') . '</p>';
		} else {
			$check = false;
			echo '<p class="error">' . __d('croogo', 'Your config directory is NOT writable.') . '</p>';
		}

		// php version
		$minPhpVersion = '5.2.8';
		if (version_compare(phpversion(), $minPhpVersion, '>=')) {
			echo '<p class="success">' . sprintf(__d('croogo', 'PHP version %s >= %s'), phpversion(), $minPhpVersion) . '</p>';
		} else {
			$check = false;
			echo '<p class="error">' . sprintf(__d('croogo', 'PHP version %s < %s'), phpversion(), $minPhpVersion) . '</p>';
		}

		// cakephp version
		$minCakeVersion = '2.3.0';
		$cakeVersion = Configure::version();
		if (version_compare($cakeVersion, $minCakeVersion, '>=')) {
			echo '<p class="success">' . __d('croogo', 'CakePhp version %s >= %s', $cakeVersion, $minCakeVersion) . '</p>';
		} else {
			$check = false;
			echo '<p class="error">' . __d('croogo', 'CakePHP version %s < %s', $cakeVersion, $minCakeVersion) . '</p>';
		}

?>
</div>
<?php
if ($check) {
	$out = $this->Html->link(__d('croogo', 'Install'), array(
		'action' => 'database',
	), array(
		'button' => 'success',
		'tooltip' => array(
			'data-title' => __d('croogo', 'Click here to begin installation'),
			'data-placement' => 'left',
		),
	));
} else {
	$out = '<p>' . __d('croogo', 'Installation cannot continue as minimum requirements are not met.') . '</p>';
}
echo $this->Html->div('form-actions', $out);
?>

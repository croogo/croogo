<div class="install">
    <h2><?php echo $this->fetch('title'); ?></h2>
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
    if (is_writable(ROOT . DS . 'config')) {
        echo '<p class="success">' . __d('croogo', 'Your config directory is writable.') . '</p>';
    } else {
        $check = false;
        echo '<p class="error">' . __d('croogo', 'Your config directory is NOT writable.') . '</p>';
    }

    $versions = \Croogo\Install\InstallManager::versionCheck();
    if ($versions['php']) {
        echo '<p class="success">' .
            sprintf(__d('croogo', 'PHP version %s >= %s'), phpversion(), \Croogo\Install\InstallManager::PHP_VERSION) .
            '</p>';
    } else {
        $check = false;
        echo '<p class="error">' . sprintf(__d('croogo', 'PHP version %s < %s'), phpversion(), \Croogo\Install\InstallManager::PHP_VERSION) . '</p>';
    }

    // cakephp version
    if ($versions['cake']) {
        echo '<p class="success">' .
            __d('croogo', 'CakePhp version %s >= %s', \Cake\Core\Configure::version(), \Croogo\Install\InstallManager::CAKE_VERSION) .
            '</p>';
    } else {
        $check = false;
        echo '<p class="error">' . __d('croogo', 'CakePHP version %s < %s', \Cake\Core\Configure::version(), \Croogo\Install\InstallManager::CAKE_VERSION) . '</p>';
    }

    ?>
</div>
<?php
if ($check) {
    $out = $this->Html->link(__d('croogo', 'Install'), [
        'action' => 'database',
    ], [
        'button' => 'success',
        'tooltip' => [
            'data-title' => __d('croogo', 'Click here to begin installation'),
            'data-placement' => 'left',
        ],
    ]);
} else {
    $out = '<p>' . __d('croogo', 'Installation cannot continue as minimum requirements are not met.') . '</p>';
}
echo $this->Html->div('form-actions', $out);
?>

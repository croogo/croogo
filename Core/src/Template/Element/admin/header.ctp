<?php

use Cake\Core\Configure;
use Croogo\Core\Nav;
use Croogo\Core\Utility\StringConverter;

$dashboardUrl = (new StringConverter())->linkStringToArray(
    Configure::read('Site.dashboard_url')
);

?>
<header class="main-header" id="header">

    <nav class="navbar">

    <button id="sidebar-toggler" class="sidebar-toggle">
        <span class="sr-only">Toggle navigation</span>
    </button>

    <?php $this->Html->link(Configure::read('Site.title'), $dashboardUrl,
        ['class' => 'navbar-brand']); ?>

    <?php $this->Croogo->adminMenus(Nav::items('top-left'), [
        'type' => 'dropdown',
        'htmlAttributes' => [
            'id' => 'top-left-menu',
            //'class' => 'navbar-nav d-none d-sm-block mr-auto',
        ],
    ]);
?>
    <?php if ($this->getRequest()->getSession()->read('Auth.User.id')) : ?>
        <?php
        echo $this->Croogo->adminMenus(Nav::items('top-right'), [
            'type' => 'dropdown',
            'htmlAttributes' => [
                'id' => 'top-right-menu',
                'class' => 'ml-auto',
            ],
        ]);
        ?>
    <?php endif; ?>
    </nav>
</header>

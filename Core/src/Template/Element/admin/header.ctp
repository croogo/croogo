<?php

use Cake\Core\Configure;
use Croogo\Core\Nav;

$dashboardUrl = Configure::read('Croogo.dashboardUrl');

?>
<header class="navbar navbar-dark bg-black navbar-fixed-top">
    <div class="<?php echo $this->Theme->getCssClass('container'); ?>">
        <?= $this->Html->link(Configure::read('Site.title'), $dashboardUrl,
            ['class' => 'navbar-brand']); ?>

        <span class="hidden-xs-down">
        <?= $this->Croogo->adminMenus(Nav::items('top-left'), [
            'type' => 'dropdown',
            'htmlAttributes' => [
                'id' => 'top-left-menu',
                'class' => 'nav navbar-nav',
            ],
        ]);
        ?>
        </span>
        <?php if ($this->request->session()->read('Auth.User.id')): ?>
        <?php
            echo $this->Croogo->adminMenus(Nav::items('top-right'), [
                'type' => 'dropdown',
                'htmlAttributes' => [
                    'id' => 'top-right-menu',
                    'class' => 'nav navbar-nav pull-right',
                ],
            ]);
        ?>
        <?php endif; ?>
    </div>
</header>

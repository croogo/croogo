<?php

use Cake\Core\Configure;
use Croogo\Core\Nav;

$dashboardUrl = Configure::read('Croogo.dashboardUrl');

?>
<header class="navbar navbar-dark bg-black navbar-fixed-top">
    <div class="<?php echo $this->Theme->getCssClass('container'); ?>">
        <span class="hidden-xs">
        <?php echo $this->Html->link(Configure::read('Site.title'), $dashboardUrl,
            ['class' => 'navbar-brand']); ?>
        </span>
        <span class="hidden-sm-up">
            <?php echo $this->Html->link(__d('croogo', 'Dashboard'), $dashboardUrl, ['class' => 'navbar-brand']); ?>
        </span>
        <?php
        echo $this->Croogo->adminMenus(Nav::items('top-left'), [
            'type' => 'dropdown',
            'htmlAttributes' => [
                'id' => 'top-left-menu',
                'class' => 'nav navbar-nav',
            ],
        ]);
        ?>
        <?php if ($this->request->session()
            ->read('Auth.User.id')
        ): ?>
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

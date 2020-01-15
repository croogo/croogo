<?php

use Cake\Core\Configure;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?= $this->fetch('title') ?> - <?= $_siteTitle ?></title>
        <?php
        echo $this->element('admin/stylesheets');
        echo $this->element('admin/javascripts');

        echo $this->Layout->js();
        echo $this->Html->script([]);

        echo $this->fetch('script');
        echo $this->fetch('css');
        ?>
        <style>
            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
            }
        </style>
    </head>
    <body class="header-fixed header-dark">

        <main>

            <nav class="navbar navbar-static-top navbar-expand-lg">
                <?= $this->Html->link(__d('croogo', 'Back to') . ' ' . Configure::read('Site.title'), '/', ['class' => 'navbar-brand navbar-nav']) ?>
            </nav>

            <div class="container" style="min-height: 500px">

                <div class="content col-md-6 my-5">
                    <?= $this->fetch('content') ?>
                </div>

            </div>

             <?= $this->element('Croogo/Core.admin/footer') ?>

        </main>

    </body>
</html>

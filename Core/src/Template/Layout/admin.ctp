<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?php echo $this->fetch('title'); ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
        <?php

        echo $this->Html->css([
            'Croogo/Core.croogo-admin',
            'Croogo/Core.tether.min.css',
        ]);
        echo $this->Layout->js();
        echo $this->Html->script([
            'Croogo/Core.jquery/jquery.min.js',
            'Croogo/Core.tether.min.js',
            'Croogo/Core.bootstrap.min.js',
            'Croogo/Core.jquery/jquery.slug',
            'Croogo/Core.jquery/jquery.cookie',
            'Croogo/Core.jquery/jquery.hoverIntent.minified',
            'Croogo/Core.jquery/superfish',
            'Croogo/Core.jquery/supersubs',
            'Croogo/Core.jquery/jquery.elastic-1.6.1.js',
            'Croogo/Core.underscore-min',
            'Croogo/Core.admin',
            'Croogo/Core.sidebar',
            'Croogo/Core.choose',
            //'Croogo/Core.typeahead_autocomplete',
        ]);

        echo $this->fetch('script');
        echo $this->fetch('css');

        ?>
    </head>
    <body>
        <div id="wrap">
            <?php echo $this->element('Croogo/Core.admin/header'); ?>
            <?php echo $this->element('Croogo/Core.admin/navigation'); ?>
            <div id="content-container" class="<?= $this->Theme->getCssClass('container') ?>">
                <div id="content" class="clearfix">
                    <?php echo $this->element('Croogo/Core.admin/breadcrumb'); ?>
                    <div id="inner-content" class="<?= $this->Theme->getCssClass('columnFull') ?>">
                        <?php echo $this->Flash->render(); ?>
                        <?php echo $this->fetch('content'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $this->element('Croogo/Core.admin/footer'); ?>
        <?php
        echo $this->element('Croogo/Core.admin/initializers');
        echo $this->fetch('body-footer');

        echo $this->fetch('scriptBottom');
        echo $this->Js->writeBuffer();
        ?>
    </body>
</html>

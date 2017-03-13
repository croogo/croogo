<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?php echo $this->fetch('title'); ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
        <?php
        echo $this->Html->css([
            'Croogo/Core.croogo-admin',
        ]);
        echo $this->element('Croogo/Install.styles');
        echo $this->Html->script([
            'Croogo/Core.jquery/jquery.min',
            'Croogo/Core.croogo-admin',
        ]);
        echo $this->fetch('script');
        ?>
    </head>

    <body>

        <div id="wrap" class="install">
            <header class="navbar navbar-inverse bg-inverse navbar-fixed-top">
                <div class="<?php echo $this->Theme->getCssClass('container'); ?>">
                    <span class="navbar-brand"><?php echo __d('croogo', 'Install Croogo'); ?></span>
                </div>
            </header>

            <div id="main" class="<?php echo $this->Theme->getCssClass('container'); ?>">
                <div class="<?php echo $this->Theme->getCssClass('row'); ?>">
                    <div id="install" class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
                        <?php
                        echo $this->Layout->sessionFlash();
                        echo $this->fetch('content');
                        ?>
                    </div>
                </div>
            </div>

        </div>

        <?php echo $this->element('admin/footer'); ?>
        <?php
        $script = <<<EOF
$('[rel=tooltip],input[data-title]').tooltip();
EOF;
        $this->Js->buffer($script);

        echo $this->Js->writeBuffer();
        ?>
    </body>
</html>

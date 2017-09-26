<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?= __d('croogo', 'Installation: %s', $this->fetch('title')) ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
        <?php
        echo $this->Html->css([
            'Croogo/Core.croogo-admin',
        ]);
        echo $this->Html->script([
            'Croogo/Core.jquery/jquery.min',
            'Croogo/Core.croogo-admin',
        ]);
        echo $this->fetch('script');
        ?>
    </head>
    <body class="installer">
        <header class="navbar navbar-inverse bg-black navbar-fixed-top">
            <span class="navbar-brand"><?php echo __d('croogo', 'Install Croogo'); ?></span>
        </header>

        <div id="wrap">
            <div class="card">
                <?= $this->fetch('before') ?>
                <h3 class="card-header">
                    <?= __d('croogo', 'Installation: %s', $this->fetch('title')) ?>
                </h3>
                <div class="card-body">
                    <?php
                    echo $this->element('installer_steps');
                    echo $this->Layout->sessionFlash();
                    echo $this->fetch('content');
                    ?>
                </div>
                <?php
                if ($buttons = $this->fetch('buttons')) {
                    echo $this->Html->div('card-footer text-right', $buttons);
                }
                echo $this->fetch('after');
                ?>
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

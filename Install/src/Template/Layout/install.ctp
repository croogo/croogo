<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?= __d('croogo', 'Installation: %s', $this->fetch('title')) ?> - <?= __d('croogo', 'Croogo') ?></title>
        <style>
            .fa { min-width: 16px; }
        </style>
        <?php
        echo $this->Html->css([
            'Croogo/Core.core/croogo-admin',
            'Croogo/Core.core/select2.min',
            'Croogo/Core.core/select2-bootstrap.min',
        ]);
        echo $this->Html->script([
            'Croogo/Core.jquery/jquery.min',
            'Croogo/Core.core/croogo-bootstrap',
            'Croogo/Core.core/select2.full.min',
            'Croogo/Core.core/admin',
        ]);
        echo $this->fetch('script');
        ?>
    </head>
    <body class="installer">
        <header class="navbar navbar-dark bg-black navbar-fixed-top">
            <span class="navbar-brand"><?= __d('croogo', 'Install Croogo') ?></span>
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

        <?= $this->element('admin/footer') ?>
        <?php
        $script = <<<EOF
$('[rel=tooltip],input[data-title]').tooltip();
$('select:not(".no-select2")').select2({
    dropdownAutoWidth: true,
    theme: 'bootstrap'
});
EOF;
        $this->Js->buffer($script);

        echo $this->Js->writeBuffer();
        ?>
    </body>
</html>

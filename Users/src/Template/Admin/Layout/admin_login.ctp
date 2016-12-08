<?php
use Cake\Core\Configure;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?= $this->fetch('title'); ?> - <?= $_siteTitle ?></title>
        <?php
        echo $this->Html->css([
            'Croogo/Core.croogo-admin',
        ]);
        echo $this->Layout->js();
        echo $this->Html->script([
        ]);

        echo $this->fetch('script');
        echo $this->fetch('css');
        ?>
    </head>
    <body class="admin-login">
        <div id="wrap">
            <header class="navbar navbar-dark bg-inverse navbar-full">
                <div class="<?php echo $this->Theme->getCssClass('container'); ?>">
                    <?php echo $this->Html->link(__d('croogo', 'Back to') . ' ' . Configure::read('Site.title'),
                        '/', ['class' => 'navbar-brand']);
                    ?>
                </div>
            </header>

            <div id="content-container" class="<?php echo $this->Theme->getCssClass('container'); ?>">
                <div class="<?php echo $this->Theme->getCssClass('row'); ?>">
                    <div class="admin-login-box">
                        <?php
                        echo $this->Flash->render('auth');
                        echo $this->fetch('content');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $this->element('Croogo/Core.admin/footer'); ?>
    </body>
</html>

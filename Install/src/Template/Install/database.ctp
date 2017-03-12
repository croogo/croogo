<?php
$this->assign('title', __d('croogo', 'Step 1: Database'));
echo $this->Form->create(null, [
    'align' => 'horizontal',
]);
?>
    <div class="install">
        <h2><?php echo $this->fetch('title'); ?></h2>

        <?php if ($currentConfiguration['exists']): ?>
            <div class="alert alert-warning">
                <strong><?php echo __d('croogo', 'Warning'); ?>:</strong>
                <?php echo __d('croogo', 'A database configuration already exists.'); ?>
                <?php
                if ($currentConfiguration['valid']):
                    $valid = __d('croogo', 'Valid');
                    $class = 'text-success';
                else:
                    $valid = __d('croogo', 'Invalid');
                    $class = 'text-error';
                endif;
                echo __d('croogo', 'This file is %s.', $this->Html->tag('span', $valid, compact('class')));
                ?>
                <?php if ($currentConfiguration['valid']): ?>
                    <?php
                    echo $this->Html->link(__d('croogo', 'Reuse this file and proceed.'), ['action' => 'data']);
                    ?>
                <?php else: ?>
                    <?php echo __d('croogo', 'This file will be replaced.'); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php
        echo $this->Form->input('driver', [
            'placeholder' => __d('croogo', 'Database'),
            'default' => Cake\Database\Driver\Mysql::class,
            'empty' => false,
            'options' => [
                Cake\Database\Driver\Mysql::class => 'MySQL',
                Cake\Database\Driver\Sqlite::class => 'SQLite',
                Cake\Database\Driver\Postgres::class => 'PostGresql',
                Cake\Database\Driver\Sqlserver::class => 'Microsoft SQL Server',
            ],
        ]);
        echo $this->Form->input('host', [
            'placeholder' => __d('croogo', 'Host'),
            'default' => 'localhost',
            'tooltip' => __d('croogo', 'Database hostname or IP Address'),
            'prepend' => $this->Html->icon('home'),
            'label' => __d('croogo', 'Host'),
        ]);
        echo $this->Form->input('login', [
            'placeholder' => __d('croogo', 'Login'),
            'default' => 'root',
            'tooltip' => __d('croogo', 'Database login/username'),
            'prepend' => $this->Html->icon('user'),
            'label' => __d('croogo', 'Login'),
        ]);
        echo $this->Form->input('password', [
            'placeholder' => __d('croogo', 'Password'),
            'tooltip' => __d('croogo', 'Database password'),
            'prepend' => $this->Html->icon('key'),
            'label' => __d('croogo', 'Password'),
        ]);
        echo $this->Form->input('database', [
            'placeholder' => __d('croogo', 'Name'),
            'default' => 'croogo',
            'tooltip' => __d('croogo', 'Database name'),
            'prepend' => $this->Html->icon('briefcase'),
            'label' => __d('croogo', 'Name'),
        ]);
        echo $this->Form->input('port', [
            'placeholder' => __d('croogo', 'Port'),
            'tooltip' => __d('croogo', 'Database port (leave blank if unknown)'),
            'prepend' => $this->Html->icon('asterisk'),
            'label' => __d('croogo', 'Port'),
        ]);
        ?>
    </div>
    <div class="form-actions">
        <?php echo $this->Form->button('Next step', ['class' => 'success']); ?>
    </div>
<?php echo $this->Form->end(); ?>

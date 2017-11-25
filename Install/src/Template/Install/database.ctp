<?php
$this->assign('title', __d('croogo', 'Database'));

$this->start('before');
echo $this->Form->create($context, [
    'align' => ['left' => 4, 'middle' => 8, 'right' => 0],
]);
$this->end();
?>
<?php if ($currentConfiguration['exists']): ?>
    <div class="alert alert-warning">
        <strong><?= __d('croogo', 'Warning') ?>:</strong>
        <?= __d('croogo', 'A database configuration already exists.') ?>
        <?php
        if ($currentConfiguration['valid']):
            $valid = __d('croogo', 'Valid');
            $class = 'text-success';
        else:
            $valid = __d('croogo', 'Invalid');
            $class = 'text-error';
        endif;
        echo __d('croogo', 'The configuration is %s.', $this->Html->tag('span', $valid, compact('class')));
        ?>
        <?php if ($currentConfiguration['valid']): ?>
            <?php
            echo $this->Html->link(__d('croogo', 'Reuse this configuration and proceed'), ['action' => 'data']);
            ?>
            or complete the form below to replace it.
        <?php else: ?>
            <?= __d('croogo', 'This configuration will be replaced.') ?>
        <?php endif ?>
    </div>
<?php endif ?>

<?php
echo $this->Form->input('driver', [
    'placeholder' => __d('croogo', 'Database'),
    'empty' => false,
    'options' => [
        Cake\Database\Driver\Mysql::class => 'MySQL',
        Cake\Database\Driver\Sqlite::class => 'SQLite',
        Cake\Database\Driver\Postgres::class => 'PostgreSQL',
        Cake\Database\Driver\Sqlserver::class => 'Microsoft SQL Server',
    ],
]);
echo $this->Form->input('host', [
    'placeholder' => __d('croogo', 'Host'),
    'tooltip' => __d('croogo', 'Database hostname or IP Address'),
    'prepend' => $this->Html->icon('home'),
    'label' => __d('croogo', 'Host'),
]);
echo $this->Form->input('username', [
    'placeholder' => __d('croogo', 'Login'),
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
<?php
$this->assign('buttons', $this->Form->button('Next step', ['class' => 'success']));

$this->assign('after', $this->Form->end());

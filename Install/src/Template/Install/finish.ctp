<?php
$this->assign('title', __d('croogo', 'Successful'));

use Croogo\Core\Router; ?>
<p class="success">
    <?= __d(
    'croogo',
    'The user %s has been created with administrative rights.',
    sprintf('<strong>%s</strong>', $user['username'])
);
?>
</p>

<p>
    <?= __d(
    'croogo',
    'Admin panel: %s',
    $this->Html->link(Router::url('/admin', true), Router::url('/admin', true))
) ?>
</p>

<p>
    <?php
    echo __d(
        'croogo',
        'You can start with %s or jump in and %s.',
        $this->Html->link(__d('croogo', 'configuring your site'), [
            'plugin' => 'Croogo/Settings',
            'prefix' => 'admin',
            'controller' => 'Settings',
            'action' => 'prefix',
            'Site',
        ]),
        $this->Html->link(__d('croogo', 'create a blog post'), [
            'plugin' => 'Croogo/Nodes',
            'prefix' => 'admin',
            'controller' => 'Nodes',
            'action' => 'add',
            'blog',
        ])
    );
    ?>
</p>

<blockquote>
    <h3><?= __d('croogo', 'Resources') ?></h3>
    <ul class="bullet">
        <li><?= $this->Html->link('http://croogo.org') ?></li>
        <li><?= $this->Html->link('http://wiki.croogo.org/') ?></li>
        <li><?= $this->Html->link('http://github.com/croogo/croogo') ?></li>
        <li><?= $this->Html->link(
            'Croogo Google Group',
            'https://groups.google.com/forum/#!forum/croogo'
        ) ?></li>
    </ul>
</blockquote>

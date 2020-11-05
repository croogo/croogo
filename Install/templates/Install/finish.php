<?php
/**
 * @var \App\View\AppView $this
 * @var array $user
 */
$this->assign('title', __d('croogo', 'Successful'));

use Croogo\Core\Routing\Router; ?>
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

<?php if (false): ?>
<p>
    <?php
    echo __d(
        'croogo',
        'You can start with %s or jump in and %s.',
        $this->Html->link(__d('croogo', 'configuring your site'), [
            'plugin' => 'Croogo/Settings',
            'prefix' => 'Admin',
            'controller' => 'Settings',
            'action' => 'prefix',
            'Site',
        ]),
        $this->Html->link(__d('croogo', 'create a blog post'), [
            'plugin' => 'Croogo/Nodes',
            'prefix' => 'Admin',
            'controller' => 'Nodes',
            'action' => 'add',
            'blog',
        ])
    );
    ?>
</p>
<?php endif; ?>

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

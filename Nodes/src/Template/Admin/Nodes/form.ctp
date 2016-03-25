<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_edit');
$this->Html->script(array('Croogo/Nodes.admin'), ['block' => true]);

$this->CroogoHtml->addCrumb('', '/admin', ['icon' => 'home'])
    ->addCrumb(__d('croogo', 'Content'), ['action' => 'index']);

if ($this->request->params['action'] == 'add') {
    $this->assign('title', __d('croogo', 'Create content: %s', $type->title));

    $formUrl = ['action' => 'add', $typeAlias];
    $this->CroogoHtml->addCrumb(__d('croogo', 'Create'), ['action' => 'create'])
        ->addCrumb($type->title, '/' . $this->request->url);
}

if ($this->request->params['action'] == 'edit') {
    $formUrl = ['action' => 'edit'];
    $this->CroogoHtml->addCrumb($node->title, '/' . $this->request->url);
}

$lookupUrl = $this->Url->build([
    'plugin' => 'Croogo/Users',
    'controller' => 'Users',
    'action' => 'lookup',
    '_ext' => 'json',
]);

$parentTitle = isset($parentTitle) ? $parentTitle : null;
$apiUrl = $this->Url->build([
    'action' => 'lookup',
    '_ext' => 'json',
    '?' => [
        'type' => $type->alias,
    ],
]);

echo $this->CroogoForm->create($node, [
    'url' => $formUrl,
    'class' => 'protected-form',
]);

?>
<div class="row-fluid">
    <div class="span8">

        <ul class="nav nav-tabs">
            <?php
            echo $this->Croogo->adminTab(__d('croogo', $type->title), '#node-main');
            echo $this->Croogo->adminTab(__d('croogo', 'Access'), '#node-access');
            echo $this->Croogo->adminTabs();
            ?>
        </ul>

        <div class="tab-content">

            <div id="node-main" class="tab-pane">
                <?php
                echo $this->CroogoForm->input('id');
                echo $this->CroogoForm->input('title', [
                    'label' => __d('croogo', 'Title'),
                ]);
                echo $this->CroogoForm->input('slug', [
                    'class' => 'slug',
                    'label' => __d('croogo', 'Slug'),
                ]);
                echo $this->CroogoForm->autocomplete('parent_id', [
                    'label' => __d('croogo', 'Parent'),
                    'type' => 'text',
                    'autocomplete' => [
                        'default' => $parentTitle,
                        'data-displayField' => 'title',
                        'data-primaryKey' => 'id',
                        'data-queryField' => 'title',
                        'data-relatedElement' => '#NodeParentId',
                        'data-url' => $apiUrl,
                    ],
                ]);
                echo $this->CroogoForm->input('body', [
                    'label' => __d('croogo', 'Body'),
                    'id' => 'NodeBody',
                ]);
                echo $this->CroogoForm->input('excerpt', [
                    'label' => __d('croogo', 'Excerpt'),
                ]);
                ?>
            </div>

            <div id="node-access" class="tab-pane">
                <?php
                echo $this->CroogoForm->input('Role.Role', ['class' => false, 'multiple' => true]);
                ?>
            </div>

            <?php echo $this->Croogo->adminTabs(); ?>
        </div>

    </div>
    <div class="span4">
        <?php
        $username = isset($node->user->username) ? $node->user->username : $this->request->session()
            ->read('Auth.User.username');
        echo $this->CroogoHtml->beginBox(__d('croogo', 'Publishing')) .
            $this->CroogoForm->button(__d('croogo', 'Apply'), ['name' => 'apply']) .
            $this->CroogoForm->button(__d('croogo', 'Save'), ['button' => 'success']) .
            $this->Html->link(__d('croogo', 'Cancel'), ['action' => 'index'], ['class' => 'cancel btn btn-danger']) .
            $this->CroogoForm->input('status', [
                'legend' => false,
                'label' => false,
                'type' => 'radio',
                'class' => false,
                'default' => Status::UNPUBLISHED,
                'options' => $this->Croogo->statuses(),
            ]) .
            $this->CroogoForm->input('promote', [
                'label' => __d('croogo', 'Promoted to front page'),
                'class' => false,
            ]) .
            $this->CroogoForm->autocomplete('user_id', [
                'type' => 'text',
                'label' => __d('croogo', 'Publish as '),
                'class' => 'span10',
                'autocomplete' => [
                    'default' => $username,
                    'data-displayField' => 'username',
                    'data-primaryKey' => 'id',
                    'data-queryField' => 'name',
                    'data-relatedElement' => '#NodeUserId',
                    'data-url' => $lookupUrl,
                ],
            ]) .

            $this->CroogoForm->input('created', [
                'type' => 'text',
                'class' => 'span10 input-datetime',
            ]) .

            $this->Html->div('input-daterange', $this->CroogoForm->input('publish_start', [
                    'label' => __d('croogo', 'Publish Start'),
                    'type' => 'text',
                ]) . $this->CroogoForm->input('publish_end', [
                    'label' => __d('croogo', 'Publish End'),
                    'type' => 'text',
                ]));

        echo $this->CroogoHtml->endBox();

        echo $this->Croogo->adminBoxes();
        ?>
    </div>
</div>
<?php echo $this->CroogoForm->end(); ?>

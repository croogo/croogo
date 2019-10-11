<?php

$this->Croogo->adminScript('Croogo/Comments.admin');

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Content'), ['plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index']);

if (isset($criteria['Comment.status'])) {
    $this->Breadcrumbs->add(__d('croogo', 'Comments'), ['action' => 'index']);
    if ($criteria['Comment.status'] == '1') {
        $this->Breadcrumbs->add(__d('croogo', 'Published'), $this->getRequest()->getRequestTarget());
        $this->assign('title', __d('croogo', 'Comments: Published'));
    } else {
        $this->Breadcrumbs->add(__d('croogo', 'Awaiting approval'), $this->getRequest()->getRequestTarget());
        $this->assign('title', __d('croogo', 'Comments: Published'));
    }
} else {
    $this->Breadcrumbs->add(__d('croogo', 'Comments'), $this->getRequest()->getRequestTarget());
}

$this->append('table-footer', $this->element('Croogo/Core.admin/modal', [
    'id' => 'comment-modal',
    'class' => 'hide',
    ]));

$this->append('action-buttons');
echo $this->Croogo->adminAction(
    __d('croogo', 'Published'),
    ['action' => 'index', '?' => ['status' => '1']],
    ['class' => 'btn btn-outline-secondary btn-sm']
);
echo $this->Croogo->adminAction(
    __d('croogo', 'Awaiting approval'),
    ['action' => 'index', '?' => ['status' => '0']],
    ['class' => 'btn btn-outline-secondary btn-sm']
);
$this->end();

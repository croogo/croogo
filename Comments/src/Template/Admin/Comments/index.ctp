<?php

use Croogo\Core\Status;

$this->Croogo->adminScript('Croogo/Comments.admin');

$this->extend('Croogo/Core./Common/admin_index');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Content'), array('action' => 'index'))
	->addCrumb(__d('croogo', 'Comments'), array('action' => 'index'));


if (isset($criteria['Comment.status'])) {
	if ($criteria['Comment.status'] == '1') {
		$this->Html->addCrumb(__d('croogo', 'Published'), '/' . $this->request->url);
		$this->viewVars['title_for_layout'] = __d('croogo', 'Comments: Published');
	} else {
		$this->Html->addCrumb(__d('croogo', 'Approval'), '/' . $this->request->url);
		$this->viewVars['title_for_layout'] = __d('croogo', 'Comments: Approval');
	}
}

/**
 * Fixme: load element Croogo admin/modal
 *
$this->append('table-footer', $this->element('Croogo/Core.admin/modal', array(
	'id' => 'comment-modal',
	'class' => 'hide',
)));
*/

$this->append('actions');
	echo $this->Croogo->adminAction(
		__d('croogo', 'Published'),
		array('action' => 'index', '?' => array('status' => '1'))
	);
	echo $this->Croogo->adminAction(
		__d('croogo', 'Approval'),
		array('action' => 'index', '?' => array('status' => '0'))
	);
$this->end();


$this->append('form-start', $this->Form->create($comments, [
	'url' => ['action' => 'process']
]));

$this->start('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Form->checkbox('checkAll'),
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('name', __d('croogo', 'Name')),
		$this->Paginator->sort('email', __d('croogo', 'Email')),
		$this->Paginator->sort('node_id', __d('croogo', 'Node')),
		'',
		$this->Paginator->sort('created', __d('croogo', 'Created')),
		__d('croogo', 'Actions'),
	));
	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
	$rows = array();
	foreach ($comments as $comment) {
		$actions = array();
		$actions[] = $this->Croogo->adminRowActions($comment->id);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $comment->id),
			array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Comment' . $comment->id . 'Id',
			array(
				'icon' => $this->Theme->getIcon('delete'),
				'class' => 'delete',
				'tooltip' => __d('croogo', 'Remove this item'),
				'rowAction' => 'delete',
			),
			__d('croogo', 'Are you sure?')
		);

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$title = empty($comment->title) ? 'Comment' : $comment->title;
		$rows[] = array(
			$this->Form->checkbox('Comment.' . $comment->id . '.id', array('class' => 'row-select')),
			$comment->id,
			$comment->name,
			$comment->email,
			$this->Html->link($comment->title, array(
				'action' => 'view',
				'type' => $comment->type,
				'slug' => $comment->slug,
			)),
			$this->Html->link($this->Html->image('/croogo/img/icons/comment.png'), '#',
				array(
					'class' => 'comment-view',
					'data-title' => $title,
					'data-content' => $comment->body,
					'escape' => false
				)),
			$comment['Comment']['created'],
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
$this->end();

$this->start('bulk-action');
	echo $this->Form->input('Comment.action', array(
		'label' => false,
		'div' => 'input inline',
		'options' => array(
			'publish' => __d('croogo', 'Publish'),
			'unpublish' => __d('croogo', 'Unpublish'),
			'delete' => __d('croogo', 'Delete'),
		),
		'empty' => true,
	));
	$button = $this->Form->button(__d('croogo', 'Submit'), array(
		'type' => 'submit',
		'value' => 'submit'
	));
	echo $this->Html->div('controls', $button);
$this->end();
$this->append('form-end', $this->Form->end());

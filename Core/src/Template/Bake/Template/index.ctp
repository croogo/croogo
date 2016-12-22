<%
use Cake\Utility\Inflector;

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return !in_array($schema->columnType($field), ['binary', 'text']);
    });

if (isset($modelObject) && $modelObject->behaviors()->has('Tree')) {
    $fields = $fields->reject(function ($field) {
        return $field === 'lft' || $field === 'rght';
    });
}

if (!empty($indexColumns)) {
    $fields = $fields->take($indexColumns);
}

%>
<?php

$this->extend('Croogo/Core./Common/admin_index');
$this->Html->addCrumb(__('<%= $pluralHumanName %>'), ['action' => 'index']);

$this->append('actions');
    echo $this->Croogo->adminAction(__('New <%= $singularHumanName %>'), ['action' => 'add']);
<%

    $done = [];
    foreach ($associations as $type => $data):
        foreach ($data as $alias => $details):
            if (!empty($details['navLink']) && $details['controller'] !== $this->name && !in_array($details['controller'], $done)):
%>
        <?= $this->Croogo->adminAction(__('List <%= $this->_pluralHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'index']) ?>
        <?= $this->Croogo->adminAction(__('New <%= $this->_singularHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'add']) ?>
<%
                $done[] = $details['controller'];
            endif;
        endforeach;
    endforeach;
%>
$this->end();

$this->append('table-heading');
%>
<thead>
    <tr>
<% foreach ($fields as $field): %>
        <th scope="col"><?= $this->Paginator->sort('<%= $field %>') ?></th>
<% endforeach; %>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
</thead>
<?php
$this->end();

$this->append('table-body');

?>
<tbody>
    <?php foreach ($<%= $pluralVar %> as $<%= $singularVar %>): ?>
        <?php $actions = []; ?>
    <tr>
<%
    foreach ($fields as $field) {
        $isKey = false;
        if (!empty($associations['BelongsTo'])) {
            foreach ($associations['BelongsTo'] as $alias => $details) {
                if ($field === $details['foreignKey']) {
                    $isKey = true;
%>
        <td><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></td>
<%
                    break;
                }
            }
        }
        if ($isKey !== true) {
            if (!in_array($schema->columnType($field), ['integer', 'biginteger', 'decimal', 'float'])) {
%>
        <td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
<%
            } else {
%>
        <td><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>
<%
            }
        }
    }

    $pk = '$' . $singularVar . '->' . $primaryKey[0];
%>
<?php
        $actions[] = $this->Croogo->adminRowActions(<%= $pk %>);
        $actions[] = $this->Croogo->adminRowAction('', ['action' => 'view', <%= $pk %>], ['icon' => 'read']);
        $actions[] = $this->Croogo->adminRowAction('', ['action' => 'edit', <%= $pk %>], ['icon' => 'update']);
        $actions[] = $this->Croogo->adminRowAction('', ['action' => 'delete', <%= $pk %>], ['icon' => 'delete']);
?>
        <td class="actions">
            <div class="item-actions">
            <?= implode(' ', $actions); ?>
            </div>
        </td>
    </tr>
<?php
endforeach;

$this->end();

?>

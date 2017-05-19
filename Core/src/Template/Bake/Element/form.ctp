<%
use Cake\Utility\Inflector;

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->columnType($field) !== 'binary';
    });


$displayField = $modelObject->displayField() ?: null;

if (isset($modelObject) && $modelObject->behaviors()->has('Tree')) {
    $fields = $fields->reject(function ($field) {
        return $field === 'lft' || $field === 'rght';
    });
}

%>
<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__('<%= $pluralHumanName %>'), ['action' => 'index']);
$action = $this->request->param('action');

if ($action == 'edit'):
    $this->Breadcrumbs->add($<%= $singularVar %>-><%= $displayField %>);
else:
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->request->here());
endif;

$this->append('action-buttons');
<% if (strpos($action, 'add') === false): %>
    echo $this->Croogo->adminAction(__('Delete'),
        ['action' => 'delete', $<%= $singularVar %>-><%= $primaryKey[0] %>],
        ['confirm' => __('Are you sure you want to delete # {0}?', $<%= $singularVar %>-><%= $primaryKey[0] %>)]
    );
<% endif; %>
    echo $this->Croogo->adminAction(__('List <%= $pluralHumanName %>'),
        ['action' => 'index']
    );
<%
    $done = [];
    foreach ($associations as $type => $data) {
        foreach ($data as $alias => $details) {
            if ($details['controller'] !== $this->name && !in_array($details['controller'], $done)) {
%>
    echo $this->Croogo->adminAction(__('List <%= $this->_pluralHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'index']);
    echo $this->Croogo->adminAction(__('New <%= $this->_singularHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'add']);
<%
    $done[] = $details['controller'];
            }
        }
    }
%>
$this->end();
<%

$primaryTab = strtolower(Inflector::slug($singularHumanName, '-'));

%>
$this->append('form-start', $this->Form->create($<%= $singularVar %>));

$this->append('tab-heading');
    echo $this->Croogo->adminTab('<%= $singularHumanName %>', '#<%= $primaryTab %>');
$this->end();

$this->append('tab-content');
    echo $this->Html->tabStart('<%= $primaryTab %>');
<%
        foreach ($fields as $field) {
            if (in_array($field, $primaryKey)) {
                continue;
            }
            if (isset($keyFields[$field])) {
%>
        echo $this->Form->input('<%= $field %>', ['options' => $<%= $keyFields[$field] %>, 'empty' => true]);
<%
                continue;
            }
            if (!in_array($field, ['created', 'modified', 'updated'])) {
                $fieldData = $schema->column($field);
                if (in_array($fieldData['type'], ['date', 'datetime', 'time']) && (!empty($fieldData['null']))) {
%>
        echo $this->Form->input('<%= $field %>', ['empty' => true]);
<%
                } else {
%>
        echo $this->Form->input('<%= $field %>');
<%
                }
            }
        }
        if (!empty($associations['BelongsToMany'])) {
            foreach ($associations['BelongsToMany'] as $assocName => $assocData) {
%>
            echo $this->Form->input('<%= $assocData['property'] %>._ids', [
                'empty' => true,
                'options' => $<%= $assocData['variable'] %>,
            ]);
<%
            }
        }
%>
    echo $this->Html->tabEnd();
$this->end();

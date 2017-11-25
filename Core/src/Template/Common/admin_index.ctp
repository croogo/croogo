<?php
use Cake\Utility\Inflector;

if (empty($modelClass)) {
    $modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
    $className = lcfirst($this->name);
}
$humanName = Inflector::humanize(Inflector::underscore($modelClass));
$i18nDomain = $this->request->param('plugin') ? 'croogo' : $this->request->param('plugin');

$rowClass = $this->Theme->getCssClass('row');
$columnFull = $this->Theme->getCssClass('columnFull');
$tableClass = isset($tableClass) ? $tableClass : $this->Theme->getCssClass('tableClass');

$showActions = isset($showActions) ? $showActions : true;

$title = $this->fetch('title');
if (empty($title)):
    $this->assign('title', $this->name);
endif;

if ($pageHeading = trim($this->fetch('page-heading'))):
    echo $pageHeading;
endif;

if (empty($this->fetch('action-buttons'))) {
    if ($i18nDomain) {
        $entityName = __d($i18nDomain, $humanName);
    } else {
        $entityName = __($humanName);
    }
    $actionTitle = __d('croogo', 'New %s', $entityName);
    $this->assign('action-buttons', $this->Croogo->adminAction($actionTitle, ['action' => 'add'], ['button' => 'btn btn-success']));
}

$tableHeaders = trim($this->fetch('table-heading'));
if (!$tableHeaders && isset($displayFields)):
    $tableHeaders = [];
    foreach ($displayFields as $field => $arr):
        if ($arr['sort']):
            $tableHeaders[] = $this->Paginator->sort($field, __d($i18nDomain, $arr['label']));
        else:
            $tableHeaders[] = __d($i18nDomain, $arr['label']);
        endif;
    endforeach;
    $tableHeaders[] = __d('croogo', 'Actions');
    $tableHeaders = $this->Html->tableHeaders($tableHeaders);
endif;

$tableBody = trim($this->fetch('table-body'));
if (!$tableBody && isset($displayFields)):
    $rows = [];
    if (!empty(${lcfirst($this->name)})):

        foreach (${lcfirst($this->name)} as $item):
            $actions = [];

            if (isset($this->request->query['chooser'])):
                $title = isset($item->title) ? $item->title : null;
                $actions[] = $this->Croogo->adminRowAction(__d('croogo', 'Choose'), '#', [
                    'class' => 'item-choose',
                    'data-chooser-type' => $modelClass,
                    'data-chooser-id' => $item->id,
                ]);
            else:
                $actions[] = $this->Croogo->adminRowAction('', ['action' => 'edit', $item->id],
                    ['icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item')]);
                $actions[] = $this->Croogo->adminRowActions($item->id);
                $actions[] = $this->Croogo->adminRowAction('', [
                    'action' => 'delete',
                    $item->id,
                ], [
                    'icon' => $this->Theme->getIcon('delete'),
                    'tooltip' => __d('croogo', 'Remove this item'),
                ], __d('croogo', 'Are you sure?'));
            endif;
            $actions = $this->Html->div('item-actions', implode(' ', $actions));
            $row = [];
            foreach ($displayFields as $key => $val):
                extract($val);
                if (!is_int($key)) {
                    $val = $key;
                }
                if (strpos($val, '.') === false) {
                    $val = $modelClass . '.' . $val;
                }
                list($model, $field) = pluginSplit($val);
                $row[] = $this->Layout->displayField($item, $model, $field, compact('type', 'url', 'options'));
            endforeach;
            $row[] = $actions;
            $rows[] = $row;
        endforeach;
        $tableBody = $this->Html->tableCells($rows);
    endif;
endif;

$tableFooters = trim($this->fetch('table-footer'));

?>
    <div class="<?= $rowClass ?>">
        <div class="<?= $columnFull ?>">
            <?php
            $searchBlock = $this->fetch('search');
            if (!$searchBlock):
                $searchBlock = $this->element('Croogo/Core.admin/search');
            endif;

            if (!empty($searchBlock)) :
            ?>
            <div class="navbar navbar-light bg-light">
                <div class="float-right">
                    <?= $searchBlock ?>
                </div>
            </div>
            <?php
            endif;

            if ($contentBlock = trim($this->fetch('content'))):
                echo $this->element('Croogo/Core.admin/search');
                echo $contentBlock;
            else:
                if ($formStart = trim($this->fetch('form-start'))):
                    echo $formStart;
                endif;

                if ($mainBlock = trim($this->fetch('main'))):
                    echo $mainBlock;
                elseif ($tableBody):
                    ?>
                    <div class="table-container">
                    <table class="<?= $tableClass ?>">
                        <?php
                        echo $this->Html->tag('thead', $tableHeaders);
                        echo $this->Html->tag('tbody', $tableBody);
                        if ($tableFooters):
                            echo $this->Html->tag('tfoot', $tableFooters);
                        endif;
                        ?>
                    </table>
                    </div>
                <?php endif ?>

                <?php if ($bulkAction = trim($this->fetch('bulk-action'))): ?>
                <div id="bulk-action">
                    <?= $bulkAction ?>
                </div>
                <?php endif ?>

                <?php
                if ($formEnd = trim($this->fetch('form-end'))):
                    echo $formEnd;
                elseif ($formStart):
                    echo $this->Form->end();
                endif;
                ?>

            <?php endif ?>
        </div>
    </div>

    <div class="<?= $rowClass ?>">
        <div class="<?= $columnFull ?>">
            <?php
            if ($pagingBlock = $this->fetch('paging')):
                echo $pagingBlock;
            else:
                if (isset($this->Paginator) && isset($this->request['paging'])):
                    echo $this->element('Croogo/Core.admin/pagination');
                endif;
            endif;
            ?>
        </div>
    </div>
<?php

if ($pageFooter = trim($this->fetch('page-footer'))):
    echo $pageFooter;
endif;

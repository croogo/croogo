<?php
use Cake\Utility\Inflector;

if (empty($modelClass)) {
    $modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
    $className = strtolower($this->name);
}
$humanName = Inflector::humanize(Inflector::underscore($modelClass));
$i18nDomain = $this->request->param('plugin') ? 'croogo' : $this->request->param('plugin');

$rowClass = $this->Theme->getCssClass('row');
$columnFull = $this->Theme->getCssClass('columnFull');
$tableClass = isset($tableClass) ? $tableClass : $this->Theme->getCssClass('tableClass');

$showActions = isset($showActions) ? $showActions : true;

if ($pageHeading = trim($this->fetch('page-heading'))):
    echo $pageHeading;
endif;
?>

    <h2 class="hidden-md-up">
        <?php if ($titleBlock = $this->fetch('title')): ?>
            <?php echo $titleBlock; ?>
        <?php else: ?>
            <?php
            echo !empty($title_for_layout) ? $title_for_layout : $this->name;
            ?>
        <?php endif; ?>
    </h2>

<?php if ($showActions): ?>
    <div class="actions pull-md-right pull-lg-right btn-group">
        <?php
        if ($actionsBlock = $this->fetch('actions')):
            echo $actionsBlock;
        else:
            echo $this->Croogo->adminAction(__d('croogo', 'New %s', __d($i18nDomain, $humanName)), ['action' => 'add'],
                ['button' => 'btn btn-success']);
        endif;
        ?>
    </div>
<?php endif; ?>


<?php
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
    if (!empty(${strtolower($this->name)})):
        foreach (${strtolower($this->name)} as $item):
            $actions = [];

            if (isset($this->request->query['chooser'])):
                $title = isset($item->title) ? $item->title : null;
                $actions[] = $this->Croogo->adminRowAction(__d('croogo', 'Choose'), '#', [
                    'class' => 'item-choose',
                    'data-chooser_type' => $modelClass,
                    'data-chooser_id' => $item->id,
                ]);
            else:
                $actions[] = $this->Croogo->adminRowAction('', ['action' => 'edit', $item->id],
                    ['icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item')]);
                $actions[] = $this->Croogo->adminRowActions($item[$modelClass]['id']);
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
    <div class="<?php echo $rowClass; ?>">
        <div class="<?php echo $columnFull; ?>">
            <?php
            $searchBlock = $this->fetch('search');
            if (!$searchBlock):
                $searchBlock = $this->element('Croogo/Core.admin/search');
            endif;

            if (!empty($searchBlock)) :
            ?>
            <div class="navbar navbar-light bg-faded">
                <div class="pull-right">
                    <?= $searchBlock; ?>
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
                    <table class="<?php echo $tableClass; ?>">
                        <?php
                        echo $this->Html->tag('thead', $tableHeaders);
                        echo $this->Html->tag('tbody', $tableBody);
                        if ($tableFooters):
                            echo $this->Html->tag('tfoot', $tableFooters);
                        endif;
                        ?>
                    </table>
                <?php endif; ?>

                <?php if ($bulkAction = trim($this->fetch('bulk-action'))): ?>
                <div class="<?php echo $rowClass; ?>" id="bulk-action">
                    <?php echo $bulkAction; ?>
                </div>
            <?php endif; ?>

                <?php
                if ($formEnd = trim($this->fetch('form-end'))):
                    echo $formEnd;
                endif;
                ?>

            <?php endif; ?>
        </div>
    </div>

    <div class="<?php echo $rowClass; ?>">
        <div class="<?php echo $columnFull; ?>">
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

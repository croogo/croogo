<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $editFields
 */

use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\Datasource\EntityInterface;

if (empty($modelClass)) {
    $modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
    $className = strtolower($this->name);
}

if (isset(${Inflector::variable(Inflector::singularize($this->name))})) :
    $entity = ${Inflector::variable(Inflector::singularize($this->name))};

    if ($entity instanceof EntityInterface) :
        $what = !$entity->isNew() ? __d('croogo', 'Edit') : __d('croogo', 'Add');
    else :
        $what = __d('croogo', 'Edit');
    endif;
endif;

$title = $this->fetch('title');
if (empty($title)) :
    $this->assign('title', $this->name);
endif;

$rowClass = $this->Theme->getCssClass('row');
$columnLeft = $this->Theme->getCssClass('columnLeft');
$columnRight = $this->Theme->getCssClass('columnRight');
$columnFull = $this->Theme->getCssClass('columnFull');
$tabContentClass = $this->Theme->getCssClass('tabContentClass');

if ($pageHeading = trim($this->fetch('page-heading'))) :
    echo $pageHeading;
endif;

if ($contentBlock = trim($this->fetch('content'))) :
    echo $contentBlock;

    return;
endif;

if ($formStart = trim($this->fetch('form-start'))) :
    echo $formStart;
else :
    echo $this->Form->create($entity);
    if ($this->getRequest()->getData('id') !== null) :
        echo $this->Form->control('id');
    endif;
endif;

$tabId = 'tabitem-' . Text::slug(strtolower($modelClass), '-');

if (!$this->exists('left-column')) :
    $tabHeading = $this->fetch('tab-heading');
    if (empty($tabHeading)) :
        $tabHeading = $this->Croogo->adminTab(__d('croogo', $modelClass), "#$tabId");
    endif;
    $tabHeading .= $this->Croogo->adminTabs();

    $tabContent = trim($this->fetch('tab-content'));
    if (!$tabContent) :
        $content = '';
        foreach ($editFields as $field => $opts) :
            if (is_string($opts)) {
                $field = $opts;
                $opts = [
                    'label' => false,
                    'tooltip' => ucfirst($field),
                ];
            }
            $content .= $this->Form->control($field, $opts);
        endforeach;
    endif;

    if (empty($tabContent) && !empty($content)) :
        $tabContent = $this->Html->div('tab-pane', $content, [
            'id' => $tabId,
        ]);
    endif;
    $tabContent .= $this->Croogo->adminTabs();

    $this->start('left-column');
    echo $this->Html->tag('ul', $tabHeading, ['class' => 'nav nav-tabs']);
    echo $this->Html->div($tabContentClass, $tabContent);
    $this->end();
endif;

if (!$this->exists('right-column')) :
    $this->start('right-column');
    if ($this->exists('panels')) :
        echo $this->fetch('panels');
    else :
        if ($buttonsBlock = $this->fetch('buttons')) :
            echo $buttonsBlock;
        else :
            echo $this->Html->beginBox(__d('croogo', 'Publishing'));
            echo $this->element('Croogo/Core.admin/buttons', ['type' => $modelClass]);
            echo $this->Html->endBox();
        endif;
    endif;

    echo $this->Croogo->adminBoxes();
    $this->end();
endif;

$output = '';
$output .= $this->Html->tag('div', $this->fetch('left-column'), ['class' => $columnLeft]);
$rightColumn = $this->Html->tag('div', $this->fetch('right-column'), ['class' => 'card-columns', 'style' => 'column-count: 1']);
$output .= $this->Html->tag('div', $rightColumn, ['class' => $columnRight]);
echo $this->Html->tag('div', $output, ['class' => $rowClass]);

if ($formEnd = trim($this->fetch('form-end'))) :
    echo $formEnd;
else :
    echo $this->Form->end();
endif;

if ($pageFooter = trim($this->fetch('page-footer'))) :
    echo $pageFooter;
endif;

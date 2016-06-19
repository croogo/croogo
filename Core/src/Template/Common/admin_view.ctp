<?php

use Cake\Utility\Inflector;

if (empty($modelClass)) {
    $modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
    $className = strtolower($this->name);
}

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
    <div class="<?php echo $rowClass; ?>">
        <div class="actions <?php echo $columnFull; ?>">
            <?php
            if ($actionsBlock = $this->fetch('actions')):
                echo $actionsBlock;
            else:
                echo $this->Croogo->adminAction(__d('croogo', 'New %s',
                    __d('croogo', Inflector::singularize($this->name))), ['action' => 'add'], ['button' => 'success']);
            endif;
            ?>
        </div>
    </div>
<?php endif; ?>

    <div class="<?php echo $rowClass; ?>">
        <div class="<?php echo $columnFull; ?>">
            <?php
            if ($contentBlock = trim($this->fetch('content'))):
                echo $this->element('admin/search');
                echo $contentBlock;
            else:
                if ($mainBlock = trim($this->fetch('main'))):
                    echo $mainBlock;
                else:
                    ?>
                    <ul class="nav nav-tabs">
                        <?php
                        if ($tabHeading = $this->fetch('tab-heading')):
                            echo $tabHeading;
                        else:
                            echo $this->Croogo->adminTab(__d('croogo', $modelClass), "#$tabId");
                        endif;
                        echo $this->Croogo->adminTabs();
                        ?>
                    </ul>

                    <?php

                    $tabContent = trim($this->fetch('tab-content'));
                    if (!$tabContent):
                        $content = '';
                        foreach ($editFields as $field => $opts):
                            if (is_string($opts)) {
                                $field = $opts;
                                $opts = [
                                    'class' => 'span10',
                                    'label' => false,
                                    'tooltip' => ucfirst($field),
                                ];
                            }
                            $content .= $this->Form->input($field, $opts);
                        endforeach;
                    endif;
                    ?>

                    <?php
                    if (empty($tabContent) && !empty($content)):
                        $tabContent = $this->Html->div('tab-pane', $content, [
                            'id' => $tabId,
                        ]);
                        $tabContent .= $this->Croogo->adminTabs();
                    endif;
                    echo $this->Html->div('tab-content', $tabContent);
                    ?>
                    <?php
                endif;
            endif;
            ?>
        </div>
    </div>

<?php

if ($pageFooter = trim($this->fetch('page-footer'))):
    echo $pageFooter;
endif;

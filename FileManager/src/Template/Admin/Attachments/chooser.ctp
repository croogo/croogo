<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
    <div class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
    <?php
        echo __d('croogo', 'Sort by:');
        echo ' ' . $this->Paginator->sort('id', __d('croogo', 'Id'), array('class' => 'sort'));
        echo ', ' . $this->Paginator->sort('title', __d('croogo', 'Title'), array('class' => 'sort'));
        echo ', ' . $this->Paginator->sort('created', __d('croogo', 'Created'), array('class' => 'sort'));
    ?>
    </div>
</div>

<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
    <div class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
        <?php //echo $this->element('FileManager.admin/attachments_search'); ?>
        <hr />
    </div>
</div>
<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
    <div class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
        <ul id="attachments-for-links">
        <?php foreach ($attachments as $attachment): ?>
            <li>
            <?php
                echo $this->Html->link($attachment->asset->filename,
                    $attachment->asset->path,
                array(
                    'class' => 'item-choose',
                    'data-chooser_type' => 'Node',
                    'data-chooser_id' => $attachment->asset->id,
                    'data-chooser_title' => $attachment->asset->filename,
                    'rel' => $attachment->asset->path,
                ));

                $popup = array();
                $type = __d('croogo', $attachment->asset->mime_type);

                if (preg_match('/^image/', $attachment->asset->mime_type)):
                    $popup[] = array(
                        __d('croogo', 'Preview'),
                        [$this->Html->image($attachment->asset->path, ['class' => 'img-thumbnail']), ['class' => 'nowrap']]
                    );
                endif;
                $popup[] = array(
                    __d('croogo', 'Created'),
                    [$this->Time->nice($attachment->asset->created), ['class' => 'nowrap']]
                );
                $popup = $this->Html->tag('table', $this->Html->tableCells($popup), array(
                    'class' => 'table table-condensed',
                ));
                $a = $this->Html->link('', '#', array(
                    'class' => 'popovers action',
                    'icon' => $this->Theme->getIcon('info-sign'),
                    'data-title' => $type,
                    'data-trigger' => 'click|focus',
                    'data-placement' => 'right',
                    'data-html' => 'true',
                    'data-content' => h($popup),
                ));
                echo '&nbsp;' . $a;
            ?>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php echo $this->element('admin/pagination'); ?>
    </div>
</div>
<?php

$script =<<<EOF
$('.popovers').popover().on('click', function() { return false; });
EOF;
$this->Js->buffer($script);

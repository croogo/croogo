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
        <div id="attachments-for-links" class="card-deck">
        <?php foreach ($attachments as $attachment): ?>
            <div class="card">
                <?php
                if (preg_match('/^image/', $attachment->asset->mime_type)):
                    echo $this->Html->image($attachment->asset->path, [
                        'class' => 'card-img-top',
                    ]);
                endif;
                ?>

                <div class="card-body">
                <?php

                echo $this->Html->para(null,
                    $this->Html->link(
                        $attachment->asset->filename,
                        $attachment->asset->path,
                        [
                            'class' => 'item-choose',
                            'data-chooser_type' => 'Attachment',
                            'data-chooser_id' => $attachment->asset->id,
                            'data-chooser_title' => $attachment->asset->filename,
                            'rel' => $attachment->asset->path,
                        ]
                    )
                );

                echo $this->Html->para(null,
                    __d('croogo', 'Created') . ': ' .
                    $this->Time->nice($attachment->asset->created)
                );
                ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php echo $this->element('admin/pagination'); ?>
    </div>
</div>

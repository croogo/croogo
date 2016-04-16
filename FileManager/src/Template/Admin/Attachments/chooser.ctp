<div class="navbar navbar-light bg-faded">
    <div class="pull-left">
        <?php
        echo __d('croogo', 'Sort by:');
        echo ' ' . $this->Paginator->sort('id', __d('croogo', 'Id'), ['class' => 'sort']);
        echo ', ' . $this->Paginator->sort('title', __d('croogo', 'Title'), ['class' => 'sort']);
        echo ', ' . $this->Paginator->sort('created', __d('croogo', 'Created'), ['class' => 'sort']);
        ?>
    </div>
    <div class="pull-right">
        <?php echo $this->element('Croogo/Nodes.admin/nodes_search'); ?>
    </div>
</div>

<div class="<?php echo $this->Layout->Theme->getCssClass('row'); ?>">
    <div class="<?php echo $this->Layout->Theme->getCssClass('columnFull'); ?>">
        <div class="card-deck-wrapper">
            <div class="card-deck">
                <?php
                $rows = [];
                foreach ($attachments as $attachment):
                    list($mimeType, $imageType) = explode('/', $attachment->mime_type);
                    $imagecreatefrom = ['gif', 'jpeg', 'png', 'string', 'wbmp', 'webp', 'xbm', 'xpm'];
                    if ($mimeType == 'image' && in_array($imageType, $imagecreatefrom)) {
                        $thumbnail = $this->Image->resize($attachment->path, 400, 200, [], ['class' => 'thumbnail card-img-top']);
                    } else {
                        $thumbnail = $this->Html->image(
                            '/croogo/img/icons/page_white.png',
                            ['class' => 'card-img-top']
                        );
                    }

                    $footerText = $attachment->title .
                        '<br>' .
                        $this->Html->tag('small', $attachment->slug, ['class' => 'text-muted']);
                    $cardHeader = $this->Html->div('card-header', $footerText);
                    $card = $this->Html->div(
                        'card text-xs-center selector item-choose',
                        $cardHeader . $thumbnail,
                        [
                            'data-slug' => $attachment->slug,
                            'data-chooser-type' => 'Node',
                            'data-chooser-id' => $attachment->id,
                            'data-chooser-title' => $attachment->title,
                            'rel' => $attachment->path,
                        ]
                    );
                    echo $card;
                endforeach;
                ?>
            </div>
        </div>
        <?php echo $this->element('Croogo/Core.admin/pagination'); ?>
    </div>
</div>

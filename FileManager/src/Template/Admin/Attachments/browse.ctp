<?php
/**
 * @var \Croogo\Core\View\CroogoView $this
 */
$this->Html->script('Croogo/FileManager.browse', ['block' => true]);
?>
<div class="card-deck-wrapper">
    <div class="card-deck">
        <?php
        $rows = [];
        foreach ($attachments as $attachment):
            list($mimeType, $imageType) = explode('/', $attachment->mime_type);
            $imagecreatefrom = ['gif', 'jpeg', 'png', 'string', 'wbmp', 'webp', 'xbm', 'xpm'];
            if ($mimeType == 'image' && in_array($imageType, $imagecreatefrom)) {
                $thumbnail = $this->Image->resize($attachment->path, 400, 200, [], ['class' => 'card-img-top']);
            } else {
                $thumbnail = $this->Html->image('/croogo/img/icons/page_white.png', ['class' => 'card-img-top']);
            }

            $footerText = $attachment->title . '<br>' . $this->Html->tag('small', $attachment->slug, ['class' => 'text-muted']);
            $cardHeader = $this->Html->div('card-header', $footerText);
            $card = $this->Html->div('card text-xs-center selector', $cardHeader . $thumbnail, [
                'data-slug' => $attachment->slug
            ]);
            echo $card;
        endforeach;
        ?>
    </div>
</div>

<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
    <div class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
        <?php echo $this->element('admin/pagination'); ?>
    </div>
</div>

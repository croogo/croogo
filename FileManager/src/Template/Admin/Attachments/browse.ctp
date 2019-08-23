<?php
/**
 * @var \Croogo\Core\View\CroogoView $this
 */
$this->Html->script('Croogo/FileManager.attachments/browse', ['block' => true]);
?>
<div class="card-deck">
    <?php
    $rows = [];
    foreach ($attachments as $attachment):
        list($mimeType, $imageType) = explode('/', $attachment->mime_type);
        $imagecreatefrom = ['gif', 'jpeg', 'png', 'string', 'wbmp', 'webp', 'xbm', 'xpm'];
        if ($mimeType == 'image' && in_array($imageType, $imagecreatefrom)) {
            $thumbnail = $this->Image->resize($attachment->path, 400, 200, [], ['class' => 'card-img-top']);
        } else {
            $thumbnail = $this->Html->image('Croogo/Core./img/icons/page_white.png', [
                'class' => 'card-img-top',
            ]);
        }

        $footerText = $this->Html->tag('small', $attachment->slug, [
            'class' => 'text-muted',
        ]);

        $cardHeader = $this->Html->div('card-header', h($attachment->title));
        $cardBlock = $this->Html->div('card-body', $thumbnail);
        $cardFooter = $this->Html->div('card-footer', $footerText);
        $card = $this->Html->div('card text-center selector',
            $cardHeader . $cardBlock . $cardFooter, [
            'data-slug' => $attachment->slug,
        ]);
        echo $card;
    endforeach;
    ?>
</div>

<div class="<?= $this->Theme->getCssClass('row') ?>">
    <div class="<?= $this->Theme->getCssClass('columnFull') ?>">
        <?= $this->element('admin/pagination') ?>
    </div>
</div>

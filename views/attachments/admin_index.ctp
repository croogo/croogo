<div class="attachments index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('New Attachment', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders = $html->tableHeaders(array(
            $paginator->sort('id'),
            '&nbsp;',
            $paginator->sort('title'),
            __('URL', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($attachments AS $attachment) {
            $actions  = $html->link(__('Edit', true), array(
                'controller' => 'attachments',
                'action' => 'edit',
                $attachment['Node']['id'],
            ));
            $actions .= ' ' . $layout->adminRowActions($attachment['Node']['id']);
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'controller' => 'attachments',
                'action' => 'delete',
                $attachment['Node']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $mimeType = explode('/', $attachment['Node']['mime_type']);
            $mimeType = $mimeType['0'];
            if ($mimeType == 'image') {
                $thumbnail = $html->link($image->resize('/uploads/' . $attachment['Node']['slug'], 100, 200), array('controller' => 'attachments', 'action' => 'edit', $attachment['Node']['id']), array('escape' => false));
            } else {
                $thumbnail = $html->image('/img/icons/page_white.png') . ' ' . $attachment['Node']['mime_type'] . ' (' . $filemanager->filename2ext($attachment['Node']['slug']) . ')';
            }

            $rows[] = array(
                $attachment['Node']['id'],
                $thumbnail,
                $attachment['Node']['title'],
                $html->link($text->truncate($html->url($attachment['Node']['path'], true), 20), $attachment['Node']['path']),
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>

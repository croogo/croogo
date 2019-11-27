<div class="navbar navbar-light bg-light">
    <div class="float-left">
        <?php
        echo __d('croogo', 'Sort by:');
        echo ' ' . $this->Paginator->sort('id', __d('croogo', 'Id'), ['class' => 'sort']);
        echo ', ' . $this->Paginator->sort('title', __d('croogo', 'Title'), ['class' => 'sort']);
        echo ', ' . $this->Paginator->sort('publish_start', __d('croogo', 'Published'), ['class' => 'sort']);
        ?>
    </div>
    <div class="float-right">
        <?= $this->element('Croogo/Nodes.admin/nodes_search') ?>
        <?= $this->Form->input('chooser', ['type' => 'hidden', 'default' => true]); ?>
    </div>
</div>
<hr>
<div class="row">
    <ul id="nodes-for-links">
        <?php if (isset($type)) : ?>
        <li>
            <?php
            echo $this->Html->link(__d('croogo', '%s archive/index', h($type->title)), [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Nodes',
                'controller' => 'Nodes',
                'action' => 'hierarchy',
                'type' => $type->alias,
            ], [
                'class' => 'item-choose',
                'data-chooser-type' => 'Node',
                'data-chooser-id' => $type->id,
                'data-chooser-title' => h($type->title),
                'rel' => $type->url->toLinkString(),
            ]);
            ?>
        </li>
        <?php endif ?>
        <?php foreach ($nodes as $node) : ?>
            <li>
                <?php
                echo $this->Html->link($node->title, [
                    'prefix' => 'admin',
                    'plugin' => 'Croogo/Nodes',
                    'controller' => 'Nodes',
                    'action' => 'view',
                    'type' => $node->type,
                    'slug' => $node->slug,
                ], [
                    'class' => 'item-choose',
                    'data-chooser-type' => 'Node',
                    'data-chooser-id' => $node->id,
                    'data-chooser-title' => h($node->title),
                    'rel' => $node->url->toLinkString(),
                ]);

                $popup = [];
                $type = __d('croogo', $nodeTypes[$node->type]);
                $popup[] = [
                    __d('croogo', 'Promoted'),
                    $this->Layout->status($node->promote),
                ];
                $popup[] = [__d('croogo', 'Status'), $this->Layout->status($node->status)];
                $popup[] = [__d('croogo', 'Published'), $node->publish_start];
                $popup = implode('<br>', array_map(function ($el) {
                    return implode(': ', $el);
                }, $popup));
                $a = $this->Html->link('', '#', [
                    'class' => 'popovers action',
                    'icon' => 'info-sign',
                    'escapeTitle' => false,
                    'data-title' => h($type),
                    'data-trigger' => 'click',
                    'data-placement' => 'right',
                    'data-html' => 'true',
                    'data-content' => $popup,
                ]);
                echo "&nbsp; " . $a;
                ?>
            </li>
        <?php endforeach ?>
    </ul>
</div>
<div class="row">
    <div class="pagination-wrapper">
        <ul class="pagination justify-content-center pagination-sm">
            <?= $this->Paginator->numbers() ?>
        </ul>
    </div>
</div>
<script>
$('.popovers').popover();
</script>

<div class="navbar navbar-light bg-light">
    <div class="float-left">
        <?php
        echo __d('croogo', 'Sort by:');
        echo ' ' . $this->Paginator->sort('id', __d('croogo', 'Id'), ['class' => 'sort']);
        echo ', ' . $this->Paginator->sort('title', __d('croogo', 'Title'), ['class' => 'sort']);
        echo ', ' . $this->Paginator->sort('created', __d('croogo', 'Created'), ['class' => 'sort']);
        ?>
    </div>
    <div class="float-right">
        <?= $this->element('Croogo/Nodes.admin/nodes_search') ?>
    </div>
</div>
<hr>
<div class="row">
    <ul id="nodes-for-links">
        <?php if (isset($type)) : ?>
        <li>
            <?php
            echo $this->Html->link(__d('croogo', '%s archive/index', $type->title), [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Nodes',
                'controller' => 'Nodes',
                'action' => 'hierarchy',
                'type' => $type->alias,
            ], [
                'class' => 'item-choose',
                'data-chooser-type' => 'Node',
                'data-chooser-id' => $type->id,
                'data-chooser-title' => $type->title,
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
                    'data-chooser-title' => $node->title,
                    'rel' => $node->url->toLinkString(),
                ]);

                $popup = [];
                $type = __d('croogo', $nodeTypes[$node->type]);
                $popup[] = [
                    __d('croogo', 'Promoted'),
                    $this->Layout->status($node->promote),
                ];
                $popup[] = [__d('croogo', 'Status'), $this->Layout->status($node->status)];
                $popup[] = [__d('croogo', 'Created'), $node->created];
                $popup = $this->Html->tag('table', $this->Html->tableCells($popup));
                $a = $this->Html->link('', '#', [
                    'class' => 'popovers action',
                    'icon' => 'info-sign',
                    'data-title' => $type,
                    'data-trigger' => 'click',
                    'data-placement' => 'right',
                    'data-html' => true,
                    'data-content' => h($popup),
                ]);
                echo $a;
                ?>
            </li>
        <?php endforeach ?>
    </ul>
    <div class="pagination">
        <ul><?= $this->Paginator->numbers() ?></ul>
    </div>
</div>

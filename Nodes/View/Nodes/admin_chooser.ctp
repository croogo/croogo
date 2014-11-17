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
		<?php echo $this->element('Nodes.admin/nodes_search'); ?>
		<hr />
	</div>
</div>

<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
	<div class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
		<ul id="nodes-for-links">
		<?php foreach ($nodes as $node) { ?>
			<li>
			<?php
				echo $this->Html->link($node['Node']['title'], array(
					'admin' => false,
					'plugin' => 'nodes',
					'controller' => 'nodes',
					'action' => 'view',
					'type' => $node['Node']['type'],
					'slug' => $node['Node']['slug'],
				), array(
					'class' => 'item-choose',
					'data-chooser_type' => 'Node',
					'data-chooser_id' => $node['Node']['id'],
					'data-chooser_title' => $node['Node']['title'],
					'rel' => sprintf(
						'plugin:%s/controller:%s/action:%s/type:%s/slug:%s',
						'nodes',
						'nodes',
						'view',
						$node['Node']['type'],
						$node['Node']['slug']
					),
				));

				$popup = array();
				$type = __d('croogo', $nodeTypes[$node['Node']['type']]);
				$popup[] = array(
					__d('croogo', 'Promoted'),
					$this->Layout->status($node['Node']['promote'])
				);
				$popup[] = array(
					__d('croogo', 'Status'),
					$this->Layout->status($node['Node']['status'])
				);
				$popup[] = array(
					__d('croogo', 'Created'),
					array($this->Time->niceShort($node['Node']['created']), array('class' => 'nowrap'))
				);
				$popup = $this->Html->tag('table', $this->Html->tableCells($popup), array(
					'class' => 'table table-condensed',
				));
				$a = $this->Html->link('', '#', array(
					'class' => 'popovers action',
					'icon' => $this->Theme->getIcon('info-sign'),
					'data-title' => $type,
					'data-trigger' => 'click',
					'data-placement' => 'right',
					'data-html' => true,
					'data-content' => h($popup),
				));
				echo $a;
			?>
			</li>
		<?php } ?>
		</ul>
		<?php echo $this->element('admin/pagination'); ?>
	</div>
</div>
<?php

$script =<<<EOF
$('.popovers').popover().on('click', function() { return false; });;
EOF;
$this->Js->buffer($script);

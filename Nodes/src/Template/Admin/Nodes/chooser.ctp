<div class="row-fluid">
	<div class="span12">
	<?php
		echo __d('croogo', 'Sort by:');
		echo ' ' . $this->Paginator->sort('id', __d('croogo', 'Id'), array('class' => 'sort'));
		echo ', ' . $this->Paginator->sort('title', __d('croogo', 'Title'), array('class' => 'sort'));
		echo ', ' . $this->Paginator->sort('created', __d('croogo', 'Created'), array('class' => 'sort'));
	?>
	</div>
</div>

<div class="row-fluid">
	<?php
		echo $this->element('Croogo/Nodes.admin/nodes_search');
	?>

	<hr />

	<ul id="nodes-for-links">
	<?php foreach ($nodes as $node) { ?>
		<li>
		<?php
			echo $this->CroogoHtml->link($node->title, array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Nodes',
				'controller' => 'Nodes',
				'action' => 'view',
				'type' => $node->type,
				'slug' => $node->slug,
			), array(
				'class' => 'item-choose',
				'data-chooser_type' => 'Node',
				'data-chooser_id' => $node->id,
				'data-chooser_title' => $node->title,
				'rel' => sprintf(
					'plugin:%s/controller:%s/action:%s/type:%s/slug:%s',
					'nodes',
					'nodes',
					'view',
					$node->type,
					$node->slug
					),
			));

			$popup = array();
			$type = __d('croogo', $nodeTypes[$node->type]);
			$popup[] = array(__d('croogo', 'Promoted'), $this->Layout->status($node->promote)
			);
			$popup[] = array(__d('croogo', 'Status'), $this->Layout->status($node->status));
			$popup[] = array(__d('croogo', 'Created'), $node->created);
			$popup = $this->CroogoHtml->tag('table', $this->CroogoHtml->tableCells($popup));
			$a = $this->CroogoHtml->link('', '#', array(
				'class' => 'popovers action',
				'icon' => 'info-sign',
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
	<div class="pagination"><ul><?php echo $this->Paginator->numbers(); ?></ul></div>
</div>
<?php

$this->CroogoHtml->scriptBlock('$(\'.popovers\').popover().on(\'click\', function() { return false; });', ['block' => 'scriptBottom']);

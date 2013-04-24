<div class="node-more-info">
<?php
	$type = $types_for_layout[$this->Nodes->field('type')];

	if (is_array($this->Nodes->node['Taxonomy']) && count($this->Nodes->node['Taxonomy']) > 0) {
		$nodeTerms = Hash::combine($this->Nodes->node, 'Taxonomy.{n}.Term.slug', 'Taxonomy.{n}.Term.title');
		$nodeTermLinks = array();
		if (count($nodeTerms) > 0) {
			foreach ($nodeTerms as $termSlug => $termTitle) {
				$nodeTermLinks[] = $this->Html->link($termTitle, array(
					'plugin' => 'nodes',
					'controller' => 'nodes',
					'action' => 'term',
					'type' => $this->Nodes->field('type'),
					'slug' => $termSlug,
				));
			}
			echo __d('croogo', 'Posted in') . ' ' . implode(', ', $nodeTermLinks);
		}
	}

	if ($this->params['action'] != 'view' && $type['Type']['comment_status']) {
		if (isset($nodeTerms) && count($nodeTerms) > 0) {
			echo ' | ';
		}

		$commentCount = '';
		if ($this->Nodes->field('comment_count') == 0) {
			$commentCount = __d('croogo', 'Leave a comment');
		} elseif ($this->Nodes->field('comment_count') == 1) {
			$commentCount = $this->Nodes->field('comment_count') . ' ' . __d('croogo', 'Comment');
		} else {
			$commentCount = $this->Nodes->field('comment_count') . ' ' . __d('croogo', 'Comments');
		}
		echo $this->Html->link($commentCount, $this->Html->url($this->Nodes->field('url'), true) . '#comments');
	}
?>
</div>
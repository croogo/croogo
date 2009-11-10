<div class="node-more-info">
<?php
    $type = $types_for_layout[$node['Node']['type']];

    if (is_array($node['Term']) && count($node['Term']) > 0) {
        $nodeTerms = Set::combine($node, 'Term.{n}.slug', 'Term.{n}.title');
        $nodeTermLinks = array();
        if (count($nodeTerms) > 0) {
            foreach ($nodeTerms AS $termSlug => $termTitle) {
                $nodeTermLinks[] = $html->link($termTitle, array(
                    'controller' => 'nodes',
                    'action' => 'term',
                    'type' => $node['Node']['type'],
                    'slug' => $termSlug,
                ));
            }
            echo __('Posted in') . ' ' . implode(', ', $nodeTermLinks);
        }
    }

    if ($this->params['action'] != 'view' &&
        $type['Type']['comment_status']) {
        if (isset($nodeTerms) && count($nodeTerms) > 0) {
            echo ' | ';
        }

        $commentCount = '';
        if ($node['Node']['comment_count'] == 0) {
            $commentCount = __('Leave a comment', true);
        } elseif ($node['Node']['comment_count'] == 1) {
            $commentCount = $node['Node']['comment_count'] . ' ' . __('Comment', true);
        } else {
            $commentCount = $node['Node']['comment_count'] . ' ' . __('Comments', true);
        }
        echo $html->link($commentCount, $html->url($node['Node']['url'], true) . '#comments');
    }
?>
</div>
<div class="node-more-info text-muted mb-5">
<?php
    $type = $typesForLayout[$this->Nodes->field('type')];

    if (!empty($this->Nodes->node->taxonomies)) {
        echo __d('croogo', 'Posted in') . ' ' . implode(', ', $this->Nodes->nodeTermLinks());
    }

    if ($this->Nodes->commentsEnabled() && $this->request->params['action'] !== 'view' && $type->comment_status) {
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
        echo $this->Html->link($commentCount, array_merge($this->Nodes->field('url')->getUrl()), ['#' => 'comments']);
    }
?>
</div>

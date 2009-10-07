<?php echo __('A new comment has been posted under: ', true) . ' ' . $node['Node']['title']; ?> 

<?php echo Router::url($node['Node']['url'], true) ?>
<?php
    echo sprintf(__('A new comment has been posted under: %s', true),$node['Node']['title']) . "\n \n";

    echo Router::url($node['Node']['url'], true) . '#comment-' . $commentId . "\n \n";
    
    echo sprintf(__('Name: %s', true), $data['name']) . "\n";
    echo sprintf(__('Email: %s', true), $data['email']) . "\n";
    echo sprintf( __('Website: %s', true), $data['website']) . "\n";
    echo sprintf(__('IP Address: %s', true), $data['ip']) . "\n";
    echo sprintf(__('Comment: %s', true), $data['body']) . "\n";
?>
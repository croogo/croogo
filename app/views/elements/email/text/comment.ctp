<?php
    echo __('A new comment has been posted under: ', true) . $node['Node']['title'] . "\n \n";

    echo Router::url($node['Node']['url'], true) . '#comment-' . $commentId . "\n \n";
    
    echo __('Name: ', true) . $data['name'] . "\n";
    echo __('Email: ', true) . $data['email'] . "\n";
    echo __('Website: ', true) . $data['website'] . "\n";
    echo __('IP Address: ', true) . $data['ip'] . "\n";
    echo __('Comment: ', true) . $data['body'] . "\n";
?>
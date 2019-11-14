<?php

use Phinx\Seed\AbstractSeed;

class TypesSeed extends AbstractSeed
{

    public $table = 'types';

    public $records = [
        [
            'id' => '1',
            'title' => 'Page',
            'alias' => 'page',
            'description' => 'A page is a simple method for creating and displaying information that rarely changes, such as an "About us" section of a website. By default, a page entry does not allow visitor comments.',
            'format_show_author' => '0',
            'format_show_date' => '0',
            'comment_status' => '0',
            'comment_approve' => '1',
            'comment_spam_protection' => '0',
            'comment_captcha' => '0',
            'params' => 'routes=true',
            'plugin' => null,
            'created_by' => 1,
        ],
        [
            'id' => '2',
            'title' => 'Blog',
            'alias' => 'blog',
            'description' => 'A blog entry is a single post to an online journal, or blog.',
            'format_show_author' => '1',
            'format_show_date' => '1',
            'comment_status' => '2',
            'comment_approve' => '1',
            'comment_spam_protection' => '0',
            'comment_captcha' => '0',
            'params' => 'routes=true',
            'plugin' => null,
            'created_by' => 1,
        ],
        [
            'id' => '4',
            'title' => 'Post',
            'alias' => 'post',
            'description' => 'Default content type.',
            'format_show_author' => '1',
            'format_show_date' => '1',
            'comment_status' => '2',
            'comment_approve' => '1',
            'comment_spam_protection' => '0',
            'comment_captcha' => '0',
            'params' => 'routes=true',
            'plugin' => null,
            'created_by' => 1,
        ],
    ];

    public function run()
    {
        $Table = $this->table('types');
        $Table->insert($this->records)->save();
    }
}

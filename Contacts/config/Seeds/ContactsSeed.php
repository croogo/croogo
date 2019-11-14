<?php

use Phinx\Seed\AbstractSeed;

class ContactsSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '1',
            'title' => 'Contact',
            'alias' => 'contact',
            'body' => '',
            'name' => '',
            'position' => '',
            'address' => '',
            'address2' => '',
            'state' => '',
            'country' => '',
            'postcode' => '',
            'phone' => '',
            'fax' => '',
            'email' => 'you@your-site.com',
            'message_status' => '1',
            'message_archive' => '0',
            'message_count' => '0',
            'message_spam_protection' => '0',
            'message_captcha' => '0',
            'message_notify' => '1',
            'status' => '1',
            'created_by' => 1,
        ],
    ];

    public function run()
    {
        $Table = $this->table('contacts');
        $Table->insert($this->records)->save();
    }
}

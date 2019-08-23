<?php

use Phinx\Seed\AbstractSeed;

class BlocksSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '3',
            'region_id' => '4',
            'title' => 'About',
            'alias' => 'about',
            'body' => 'This is the content of your block. Can be modified in admin panel.',
            'show_title' => '1',
            'class' => '',
            'status' => '1',
            'weight' => '2',
            'element' => '',
            'visibility_roles' => '',
            'visibility_paths' => '',
            'visibility_php' => '',
            'params' => '',
            'updated' => '2009-12-20 03:07:39',
            'created' => '2009-07-26 17:13:14'
        ],
        [
            'id' => '8',
            'region_id' => '4',
            'title' => 'Search',
            'alias' => 'search',
            'body' => '',
            'show_title' => '0',
            'class' => '',
            'status' => '1',
            'weight' => '1',
            'element' => 'Croogo/Nodes.search',
            'visibility_roles' => '',
            'visibility_paths' => '',
            'visibility_php' => '',
            'params' => '',
            'updated' => '2009-12-20 03:07:39',
            'created' => '2009-12-20 03:07:27'
        ],
        [
            'id' => '5',
            'region_id' => '4',
            'title' => 'Meta',
            'alias' => 'meta',
            'body' => '[menu:meta]',
            'show_title' => '1',
            'class' => '',
            'status' => '1',
            'weight' => '6',
            'element' => '',
            'visibility_roles' => '',
            'visibility_paths' => '',
            'visibility_php' => '',
            'params' => '',
            'updated' => '2009-12-22 05:17:39',
            'created' => '2009-09-12 06:36:22'
        ],
        [
            'id' => '6',
            'region_id' => '4',
            'title' => 'Blogroll',
            'alias' => 'blogroll',
            'body' => '[menu:blogroll]',
            'show_title' => '1',
            'class' => '',
            'status' => '1',
            'weight' => '4',
            'element' => '',
            'visibility_roles' => '',
            'visibility_paths' => '',
            'visibility_php' => '',
            'params' => '',
            'updated' => '2009-12-20 03:07:33',
            'created' => '2009-09-12 23:33:27'
        ],
        [
            'id' => '7',
            'region_id' => '4',
            'title' => 'Categories',
            'alias' => 'categories',
            'body' => '[vocabulary:categories type="blog"]',
            'show_title' => '1',
            'class' => '',
            'status' => '1',
            'weight' => '3',
            'element' => '',
            'visibility_roles' => '',
            'visibility_paths' => '',
            'visibility_php' => '',
            'params' => '',
            'updated' => '2009-12-20 03:07:36',
            'created' => '2009-10-03 16:52:50'
        ],
        [
            'id' => '9',
            'region_id' => '4',
            'title' => 'Recent Posts',
            'alias' => 'recent_posts',
            'body' => '[node:recent_posts conditions="Nodes.type:blog" order="Nodes.id DESC" limit="5"]',
            'show_title' => '1',
            'class' => '',
            'status' => '1',
            'weight' => '5',
            'element' => '',
            'visibility_roles' => '',
            'visibility_paths' => '',
            'visibility_php' => '',
            'params' => '',
            'updated' => '2010-04-08 21:09:31',
            'created' => '2009-12-22 05:17:32'
        ],
    ];

    public function getDependencies() {
        return [
            'RegionsSeed',
        ];
    }

    public function run()
    {
        $Table = $this->table('blocks');
        $Table->insert($this->records)->save();
    }

}

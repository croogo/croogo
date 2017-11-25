<?php

namespace Croogo\Blocks\Test\TestCase\Controller\Component;

use Croogo\Core\TestSuite\IntegrationTestCase;

class BlocksComponentTest extends IntegrationTestCase
{
    public $fixtures = [
        'plugin.croogo/blocks.block',
        'plugin.croogo/blocks.region',
        'plugin.croogo/menus.menu',
        'plugin.croogo/menus.link',
        'plugin.croogo/taxonomy.type',
        'plugin.croogo/taxonomy.vocabulary',
        'plugin.croogo/taxonomy.taxonomy',
        'plugin.croogo/taxonomy.term',
        'plugin.croogo/taxonomy.model_taxonomy',
        'plugin.croogo/comments.comment',
        'plugin.croogo/meta.meta',
        'plugin.croogo/nodes.node',
        'plugin.croogo/users.role',
        'plugin.croogo/users.user',
        'plugin.croogo/users.aro',
        'plugin.croogo/users.aco',
        'plugin.croogo/users.aros_aco',
    ];

    /**
     * test that public Blocks are displayed
     */
    public function testBlockGenerationForPublic()
    {
        $this->user('yvonne');
        $this->get('/');

        $this->assertEmpty(collection($this->viewVariable('blocksForLayout')['right'])->match([
            'title' => 'Block Visible by Admin or Registered'
        ])->toArray(), '\'Block Visible by Admin or Registered\' should not be visible for public role');

        $this->assertNotEmpty(collection($this->viewVariable('blocksForLayout')['right'])->match([
            'title' => 'Block Visible by Public'
        ])->toArray(), '\'Block Visible by Public\' should be visible for public role');
    }

    /**
     * test that block are displayed for Registered
     */
    public function testBlockGenerationForRegistered()
    {
        $this->user('registered-user');

        $this->get('/');

        $this->assertEmpty(collection($this->viewVariable('blocksForLayout')['right'])->match([
            'title' => 'Block Visible by Public'
        ])->toArray(), '\'Block Visible by Public\' should not be visible for registered role');

        $this->assertNotEmpty(collection($this->viewVariable('blocksForLayout')['right'])->match([
            'title' => 'Block Visible by Admin or Registered'
        ])->toArray(), '\'Block Visible by Admin or Registered\' should be visible for registered role');
    }

    /**
     * test that block are displayed for Admin
     */
    public function testBlockGenerationForAdmin()
    {
        $this->user('admin');

        $this->get('/');

        $this->assertEmpty(collection($this->viewVariable('blocksForLayout')['right'])->match([
            'title' => 'Block Visible by Public'
        ])->toArray(), '\'Block Visible by Public\' should not be visible for registered role');

        $this->assertNotEmpty(collection($this->viewVariable('blocksForLayout')['right'])->match([
            'title' => 'Block Visible by Admin or Registered'
        ])->toArray(), '\'Block Visible by Admin or Registered\' should be visible for registered role');
    }
}

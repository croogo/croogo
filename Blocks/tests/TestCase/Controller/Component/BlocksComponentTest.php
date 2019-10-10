<?php

namespace Croogo\Blocks\Test\TestCase\Controller\Component;

use Croogo\Core\TestSuite\IntegrationTestCase;

class BlocksComponentTest extends IntegrationTestCase
{
    public $fixtures = [
        'plugin.Croogo/Blocks.Block',
        'plugin.Croogo/Blocks.Region',
        'plugin.Croogo/Menus.Menu',
        'plugin.Croogo/Menus.Link',
        'plugin.Croogo/Taxonomy.Type',
        'plugin.Croogo/Taxonomy.Vocabulary',
        'plugin.Croogo/Taxonomy.Taxonomy',
        'plugin.Croogo/Taxonomy.Term',
        'plugin.Croogo/Taxonomy.ModelTaxonomy',
        'plugin.Croogo/Comments.Comment',
        'plugin.Croogo/Meta.Meta',
        'plugin.Croogo/Nodes.Node',
        'plugin.Croogo/Users.Role',
        'plugin.Croogo/Users.User',
        'plugin.Croogo/Users.Aro',
        'plugin.Croogo/Users.Aco',
        'plugin.Croogo/Users.ArosAco',
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

<?php

namespace Croogo\Acl\Test\TestCase\Controller;

use Croogo\TestSuite\CroogoControllerTestCase;

/**
 * AclActionsController Test
 */
class AclActionsControllerTest extends CroogoControllerTestCase
{

    /**
     * fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Croogo/Users.Aro',
        'plugin.Croogo/Users.Aco',
        'plugin.Croogo/Users.ArosAco',
        'plugin.Croogo/Users.Role',
        'plugin.Croogo/Menus.Menu',
        'plugin.Croogo/Taxonomy.Type',
        'plugin.Croogo/Taxonomy.TypesVocabulary',
        'plugin.Croogo/Taxonomy.Vocabulary',
        'plugin.Croogo/Settings.Setting',
    ];

    /**
     * testGenerateActions
     *
     * @return void
     */
    public function testGenerateActions()
    {
        $AclActions = $this->generate('Acl.AclActions', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['user'],
                'Session',
                'Menus.Menus',
                'Blocks.Blocks',
                'Nodes.Nodes',
                'Taxonomy.Taxonomies',
            ],
        ]);
        $AclActions->Auth
            ->staticExpects($this->any())
            ->method('user')
            ->will($this->returnValue(['id' => 2, 'role_id' => 1]));
        $AclActions->Session
            ->expects($this->any())
            ->method('setFlash')
            ->with(
                $this->matchesRegularExpression('/(Created Aco node:)|.*Aco Update Complete.*|(Skipped Aco node:)/'),
                $this->equalTo('flash'),
                $this->anything()
            );
        $AclActions
            ->expects($this->once())
            ->method('redirect');
        $node = $AclActions->Acl->Aco->node('controllers/Nodes');
        $this->assertNotEmpty($node);
        $AclActions->Acl->Aco->removeFromTree($node[0]['Aco']['id']);
        $this->testAction('/admin/acl/acl_actions/generate');
    }
}

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
    protected array $fixtures = [
        'plugin.Croogo/Users',
        'plugin.Croogo/Users',
        'plugin.Croogo/Users',
        'plugin.Croogo/Users',
        'plugin.Croogo/Menus',
        'plugin.Croogo/Taxonomy',
        'plugin.Croogo/Taxonomy',
        'plugin.Croogo/Taxonomy',
        'plugin.Croogo/Settings',
    ];

    /**
     * testGenerateActions
     *
     * @return void
     */
    public function testGenerateActions(): void
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

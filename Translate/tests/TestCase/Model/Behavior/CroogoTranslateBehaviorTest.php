<?php
namespace Croogo\Translate\Test\TestCase\Model\Behavior;

use App\Model\Node;
use Croogo\TestSuite\CroogoTestCase;

class CroogoTranslateBehaviorTest extends CroogoTestCase
{

    public $fixtures = [
        'plugin.users.aco',
        'plugin.users.aro',
        'plugin.users.aros_aco',
        'plugin.blocks.block',
        'plugin.comments.comment',
        'plugin.contacts.contact',
        'plugin.translate.i18n',
        'plugin.settings.language',
        'plugin.menus.link',
        'plugin.menus.menu',
        'plugin.contacts.message',
        'plugin.nodes.node',
        'plugin.meta.meta',
        'plugin.taxonomy.model_taxonomy',
        'plugin.blocks.region',
        'plugin.users.role',
        'plugin.settings.setting',
        'plugin.taxonomy.taxonomy',
        'plugin.taxonomy.term',
        'plugin.taxonomy.type',
        'plugin.taxonomy.types_vocabulary',
        'plugin.users.user',
        'plugin.taxonomy.vocabulary',
        'plugin.translate.i18n',
    ];

    public $Node = null;

/**
 * setUp
 *
 * @return void
 */
    public function setUp()
    {
        parent::setUp();
        $this->Node = ClassRegistry::init('Nodes.Node');
        if (!Plugin::loaded('Translate')) {
            Plugin::load('Translate');
        }
        $this->Node->Behaviors->attach('Translate.CroogoTranslate', [
            'fields' => [
                'title' => 'titleTranslation',
            ],
        ]);
    }

/**
 * tearDown
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Node);
        ClassRegistry::flush();
    }

/**
 * testSaveTranslation
 *
 * @return void
 */
    public function testSaveTranslation()
    {
        $translationData = [
            'Node' => [
                'title' => 'About [Translated in Bengali]',
            ],
        ];
        $this->__addNewTranslation(2, 'ben', $translationData);
        $about = $this->Node->findById('2');
        $this->assertEqual($about['Node']['title'], 'About [Translated in Bengali]');
    }

/**
 * testSaveTranslationShouldFlushCacheOfModelBeingTranslated
 */
    public function testSaveTranslationShouldFlushCacheOfModelBeingTranslated()
    {
        $translationData = ['Node' => ['title' => 'Some french content']];
        $Behaviors = $this->getMock('Behaviors', ['trigger', 'dispatchMethod']);
        $Behaviors->expects($this->any())
            ->method('trigger')
            ->with(
                $this->equalTo('afterSave'),
                $this->equalTo([$this->Node, false]),
                $this->equalTo(['breakOn' => ['Cached']])
            );

        $this->Node->Behaviors = $Behaviors;
        $this->__addNewTranslation(2, 'fra', $translationData);
    }

/**
 * __addNewTranslation
 */
    private function __addNewTranslation($id, $locale, $translationData)
    {
        $this->Node->id = $id;
        $this->Node->locale = $locale;
        $this->Node->saveTranslation($translationData);
    }
}

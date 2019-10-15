<?php
namespace Croogo\Translate\Test\TestCase\Model\Behavior;

use App\Model\Node;
use Croogo\TestSuite\CroogoTestCase;

class CroogoTranslateBehaviorTest extends CroogoTestCase
{

    public $fixtures = [
        'plugin.Croogo/Users.Aco',
        'plugin.Croogo/Users.Aro',
        'plugin.Croogo/Users.ArosAco',
        'plugin.Croogo/Blocks.Block',
        'plugin.Croogo/Comments.Comment',
        'plugin.Croogo/Contacts.Contact',
        'plugin.Croogo/Translate.I18n',
        'plugin.Croogo/Settings.Language',
        'plugin.Croogo/Menus.Link',
        'plugin.Croogo/Menus.Menu',
        'plugin.Croogo/Contacts.Message',
        'plugin.Croogo/Nodes.Node',
        'plugin.Croogo/Meta.Meta',
        'plugin.Croogo/Taxonomy.ModelTaxonomy',
        'plugin.Croogo/Blocks.Region',
        'plugin.Croogo/Users.Role',
        'plugin.Croogo/Settings.Setting',
        'plugin.Croogo/Taxonomy.Taxonomy',
        'plugin.Croogo/Taxonomy.Term',
        'plugin.Croogo/Taxonomy.Type',
        'plugin.Croogo/Taxonomy.TypesVocabulary',
        'plugin.Croogo/Users.User',
        'plugin.Croogo/Taxonomy.Vocabulary',
        'plugin.Croogo/Translate.I18n',
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

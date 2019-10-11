<?php

namespace Croogo\Taxonomy\Test\TestCase\Model;

use Croogo\TestSuite\CroogoTestCase;
use Taxonomy\Model\Term;

class TermTest extends CroogoTestCase
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
        'plugin.Croogo/Meta.Meta',
        'plugin.Croogo/Nodes.Node',
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
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Term = ClassRegistry::init('Taxonomy.Term');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Term);
    }

    public function testSaveAndGetIdShouldNotCreateNewTermWhenSlugAlreadyExists()
    {
        $oldCount = $this->Term->find('count');
        $exisitingTermData = [
            'title' => 'Uncategorized',
            'slug' => 'uncategorized',
            'description' => ''
        ];

        $this->Term->saveAndGetId($exisitingTermData);
        $newCount = $this->Term->find('count');

        $this->assertEquals($oldCount, $newCount);
    }

    public function testSaveAndGetIdShouldReturnExistingIdOfTermWhenSlugAlreadyExists()
    {
        $exisitingTermData = [
            'title' => 'Uncategorized',
            'slug' => 'uncategorized',
            'description' => ''
        ];

        $termId = $this->Term->saveAndGetId($exisitingTermData);
        $expectedId = 1;

        $this->assertEquals($expectedId, $termId);
    }

    public function testSaveAndGetIdShouldReturnNewlyCreatedIdOfTermWhenSlugIsNew()
    {
        $existingIds = $this->Term->find('all', ['fields' => ['id']]);

        $newTermData = [
            'title' => 'Bazinga',
            'slug' => 'bazinga',
            'description' => ''
        ];

        $termId = $this->Term->saveAndGetId($newTermData);

        $this->assertFalse(in_array($termId, $existingIds));
    }

    public function testSaveAndGetIdShouldShouldUpdateTermsDataWhenSlugExists()
    {
        $existingUpdatedTermData = [
                'title' => 'Uncategorized update',
                'slug' => 'uncategorized',
                'description' => 'A new description'
        ];

        $this->Term->saveAndGetId($existingUpdatedTermData);
        $termData = $this->Term->read(null, 1);
        $this->assertEquals($existingUpdatedTermData, array_intersect($existingUpdatedTermData, $termData['Term']));
    }

    public function testFindByVocabularyWithNoVocabularyIdShouldTriggerError()
    {
        $this->setExpectedException('PHPUnit_Framework_error');
        $this->Term->find('byVocabulary');
    }

    public function testFindByVocabularyShouldReturnsTermsOfVocabulary()
    {
        $terms = $this->Term->find('byVocabulary', ['vocabulary_id' => 1]);
        $termIds = Hash::extract($terms, '{n}.Term.id');
        sort($termIds);
        $expectedTermIds = [1, 2];

        $this->assertEquals($expectedTermIds, $termIds);
    }

    public function testTermIsInVocabularyShouldReturnsTrueIfTermAlreadyInVocabulary()
    {
        $inVocabulary = $this->Term->isInVocabulary(1, 1);
        $this->assertTrue($inVocabulary);
    }

    public function testAddShouldAddNewTerm()
    {
        $newTermData = [
            'Taxonomy' => ['parent_id' => null],
            'Term' => [
                'title' => 'Bazinga',
                'slug' => 'bazinga',
                'description' => ''
            ]
        ];

        $this->Term->add($newTermData, 1);
        $newTerm = $this->Term->find('first', ['conditions' => ['slug' => 'bazinga']]);

        $this->assertNotEmpty($newTerm);
    }

    public function testHasSlugChangedShouldReturnTrueIfSlugChanged()
    {
        $changed = $this->Term->hasSlugChanged(1, 'drunk-robot');
        $this->assertTrue($changed);
    }

    public function testHasSlugShouldReturnFalseIfSlugStilltheSame()
    {
        $changed = $this->Term->hasSlugChanged(1, 'uncategorized');
        $this->assertFalse($changed);
    }

    public function testHasSlugChangedShouldThrowExceptionOnInvalidId()
    {
        $this->setExpectedException('NotFoundException');
        $this->Term->hasSlugChanged('invalid', 'blah');
    }

    public function testEditShouldReturnTrueWhenRecordSaved()
    {
        $record = $this->Term->find('first', ['conditions' => ['id' => '1']]);
        $record['Taxonomy'] = ['id' => 1, 'parent_id' => null];
        $edited = $this->Term->edit($record, 1);
        $this->assertTrue((bool)$edited);
    }

    public function testEditShouldUpdateRecord()
    {
        $record = $this->Term->find('first', ['conditions' => ['id' => '1']]);
        $record['Term']['slug'] = 'drifting-monkey';
        $record['Taxonomy'] = ['id' => 1, 'parent_id' => null];
        $edited = $this->Term->edit($record, 1);

        $newSlug = $this->Term->field('slug', ['id' => 1]);
        $expected = 'drifting-monkey';
        $this->assertEquals($expected, $newSlug);
    }

    public function testRemoveShouldDeleteTerm()
    {
        $oldCount = $this->Term->find('count');

        $this->Term->remove(1, 1);

        $newCount = $this->Term->find('count');
        $this->assertEquals($oldCount - 1, $newCount);
    }
}

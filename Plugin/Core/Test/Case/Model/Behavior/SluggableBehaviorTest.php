<?php
/**
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Core.Test.Case.Model.Behavior
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');
App::uses('CakeTestCase', 'TestSuite');
App::uses('SluggableBehavior', 'Core.Model/Behavior');


class TestSluggable extends Model {

	public $useDbConfig = 'test';

	public $actsAs = array(
		'Core.Sluggable' => array(
			'field' => 'name',
			'separator' => '-',
			'slugField' => 'slug',
			'lowercase' => true,
			'checkTree' => false,
		)
	);

}

/**
 * @property TestSluggable $TestSluggable
 */
class SluggableBehaviorTest extends CakeTestCase {

	public $fixtures = array('plugin.core.test_sluggable');

	public function setUp() {
		$this->TestSluggable = new TestSluggable();

		parent::setUp();
	}

	public function testGenerateSlug() {
		$str = 'slugs are great';
		$expected = 'slugs-are-great';
		$result = $this->TestSluggable->generateSlug($str);
		$this->assertEqual($expected, $result);

		$str = "Mess'd up --text-- just (to) stress /test/ ?our! `little` \\clean\\ url fun.ction!?-->";
		$expected = 'messd-up-text-just-to-stress-test-our-little-clean-url-function';
		$result = $this->TestSluggable->generateSlug($str);
		$this->assertEqual($expected, $result);

		$str = "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöùúûüýÿ";
		$expected = 'aaaaaeaaeceeeeiiiidnoooooeuuuueyssaaaaaeaaeceeeeiiiienoooooeuuuueyy';
		$result = $this->TestSluggable->generateSlug($str);
		$this->assertEqual($expected, $result);

		$str = 'Ä Ö Ü ä ö ü Äu ß @ &';
		$expected = 'ae-oe-ue-ae-oe-ue-aeu-ss-at-and';
		$result = $this->TestSluggable->generateSlug($str);
		$this->assertEqual($expected, $result);

		$this->TestSluggable->Behaviors->load('Core.Sluggable', array(
			'delimiters' => array("'")
		));
		$str = "Perché l'erba è verde?"; // Italian
		$expected = 'perche-l-erba-e-verde';
		$result = $this->TestSluggable->generateSlug($str);
		$this->assertEqual($expected, $result);

		$str = "Peux-tu m'aider s'il te plaît?"; // French
		$expected = 'peux-tu-m-aider-s-il-te-plait';
		$result = $this->TestSluggable->generateSlug($str);
		$this->assertEqual($expected, $result);

		$this->TestSluggable->Behaviors->load('Core.Sluggable', array(
			'delimiters' => array(),
			'charMappings' => array(
				'ä' => 'a', 'ö' => 'o'
			)
		));
		$str = "Tänk efter nu – förr'n vi föser dig bort";
		$expected = 'tank-efter-nu-forrn-vi-foser-dig-bort';
		$result = $this->TestSluggable->generateSlug($str);
		$this->assertEqual($expected, $result);
	}

	public function testMakeSlugUnique() {
		$slug = 'hello-world';
		$expected = 'hello-world-1';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		$slug = 'bar-foo';
		$expected = 'bar-foo-2';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		$slug = 'bar-foo';
		$this->TestSluggable->data[$this->TestSluggable->alias]['id'] = 3;
		$expected = 'bar-foo';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);
	}

	public function testMakeSlugUniqueInPath() {
		$this->TestSluggable->Behaviors->load('Core.Sluggable', array(
			'checkTree' => true
		));
		$this->TestSluggable->Behaviors->load('Tree');

		$slug = 'hello-world';
		$this->TestSluggable->data[$this->TestSluggable->alias]['parent_id'] = 2;
		$expected = 'hello-world';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		$slug = 'hello-world';
		$this->TestSluggable->data[$this->TestSluggable->alias]['parent_id'] = null;
		$expected = 'hello-world-1';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		$slug = 'foo-bar';
		$this->TestSluggable->data[$this->TestSluggable->alias]['parent_id'] = null;
		$expected = 'foo-bar';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		$slug = 'foo-bar';
		$this->TestSluggable->data[$this->TestSluggable->alias]['parent_id'] = 1;
		$expected = 'foo-bar-1';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		$slug = 'foo-bar';
		$this->TestSluggable->data[$this->TestSluggable->alias]['parent_id'] = 2;
		$expected = 'foo-bar';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		$slug = 'bar-foo';
		$this->TestSluggable->data[$this->TestSluggable->alias]['parent_id'] = 2;
		$expected = 'bar-foo-2';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		unset($this->TestSluggable->data[$this->TestSluggable->alias]['parent_id']);
		$slug = 'hello-world';
		$expected = 'hello-world-1';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		$slug = 'bar-foo';
		$this->TestSluggable->data[$this->TestSluggable->alias]['id'] = 3;
		$expected = 'bar-foo';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		$slug = 'bar-foo';
		$this->TestSluggable->data[$this->TestSluggable->alias]['id'] = 4;
		$expected = 'bar-foo-1';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);

		$slug = 'bar-foo-bar';
		$this->TestSluggable->data[$this->TestSluggable->alias]['id'] = 4;
		$expected = 'bar-foo-bar';
		$result = $this->TestSluggable->makeSlugUnique($slug);
		$this->assertEqual($expected, $result);
	}

	public function testBeforeSave() {
		$savedData = $this->TestSluggable->save(array(
			$this->TestSluggable->alias => array(
				'name' => 'Testing beforeSave'
			)
		));
		$expected = 'testing-beforesave';
		$result = $savedData[$this->TestSluggable->alias]['slug'];
		$this->assertEqual($expected, $result);

		$savedData = $this->TestSluggable->save(array(
			$this->TestSluggable->alias => array(
				'name' => ''
			)
		));
		$this->assertFalse(isset($savedData[$this->TestSluggable->alias]['slug']));
	}

	public function testMissingFieldThrowsException() {
		$this->TestSluggable->Behaviors->load('Core.Sluggable', array(
			'field' => 'non_existent'
		));
		$this->setExpectedException('CakeException', 'The field `non_existent` is missing from DB table `test_sluggables`');
		$savedData = $this->TestSluggable->save(array(
			$this->TestSluggable->alias => array(
				'name' => 'Testing beforeSave'
			)
		));
	}

	public function testMissingSlugFieldThrowsException() {
		$this->TestSluggable->Behaviors->load('Core.Sluggable', array(
			'slugField' => 'non_existent'
		));
		$this->setExpectedException('CakeException', 'The field `non_existent` is missing from DB table `test_sluggables`');
		$savedData = $this->TestSluggable->save(array(
			$this->TestSluggable->alias => array(
				'name' => 'Testing beforeSave'
			)
		));
	}

	public function tearDown() {
		unset($this->TestSluggable);

		parent::tearDown();
	}

}

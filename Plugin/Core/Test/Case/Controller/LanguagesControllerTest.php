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
 * @subpackage    Wasabi.Plugin.Core.Test.Case.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CoreControllerTest', 'Core.Test/TestSuite');
App::uses('LanguagesController', 'Core.Controller');

class LanguagesTestController extends LanguagesController {

	public $redirectUrl;

	public $renderView;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	public function render($view = null, $layout = null) {
		$this->renderView = $view;
	}

}

/**
 * @property LanguagesTestController $Languages
 */

class LanguagesControllerTest extends CoreControllerTest {

	public $fixtures = array('plugin.core.core_setting', 'plugin.core.route', 'plugin.core.language');

	public function setUp() {
		$this->Languages = $this->generate('LanguagesTest');
		$this->_loginUser();

		parent::setUp();
	}

	public function tearDown() {
		unset($this->Languages);

		parent::tearDown();
	}

	public function testRequiredModelsAreSetup() {
		$this->assertTrue(in_array('Core.Language', $this->Languages->uses));
	}

	public function testIndexAction() {
		$this->testAction('/' . $this->backendPrefix . '/languages', array('method' => 'get'));

		$this->assertInternalType('string', $this->Languages->viewVars['title_for_layout']);
		$this->assertTrue(isset($this->Languages->viewVars['languages']));
		$this->assertNotEmpty($this->Languages->viewVars['languages']);
	}

	public function testAddActionGet() {
		$this->testAction('/' . $this->backendPrefix . '/languages/add', array('method' => 'get'));

		$this->assertInternalType('string', $this->Languages->viewVars['title_for_layout']);
		$this->assertNull($this->Languages->redirectUrl);
	}

	public function testAddActionPost() {
		$langCount = $this->Languages->Language->find('count');

		$this->testAction('/' . $this->backendPrefix . '/languages/add', array(
			'method' => 'post',
			'data' => array(
				'Language' => array(
					'name' => 'Added Language',
					'locale' => 'al',
					'iso' => 'alg',
					'lang' => 'ad-La',
					'available_at_frontend' => true,
					'available_at_backend' => true,
				)
			)
		));

		$this->assertEmpty($this->Languages->Language->validationErrors);
		$this->assertEqual(($langCount + 1), $this->Languages->Language->find('count'));
		$this->assertTrue($this->Languages->Language->hasAny(array('name' => 'Added Language')));
		$this->assertEqual('success', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Languages->redirectUrl);
	}

	public function testAddActionPostValidationError() {
		$langCount = $this->Languages->Language->find('count');

		$this->testAction('/' . $this->backendPrefix . '/languages/add', array(
			'method' => 'post',
			'data' => array(
				'Language' => array(
					'name' => '',
					'locale' => 'al',
					'iso' => 'alg',
					'lang' => 'ad-La',
					'available_at_frontend' => true,
					'available_at_backend' => true,
				)
			)
		));

		$this->assertNotEmpty($this->Languages->Language->validationErrors);
		$this->assertEqual($langCount, $this->Languages->Language->find('count'));
		$this->assertEqual('error', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertNull($this->Languages->redirectUrl);
	}

	public function testEditActionGet() {
		$this->testAction('/' . $this->backendPrefix . '/languages/edit/1', array('method' => 'get'));

		$this->assertInternalType('string', $this->Languages->viewVars['title_for_layout']);
		$this->assertNull($this->Languages->redirectUrl);
		$this->assertEqual('add', $this->Languages->renderView);

		$expected = $this->Languages->Language->findById(1);
		$result = $this->Languages->request->data;
		$this->assertEqual($expected, $result);
	}

	public function testEditActionGetNonExistentId() {
		$this->testAction('/' . $this->backendPrefix . '/languages/edit/99', array('method' => 'get'));

		$this->assertEqual('error', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Languages->redirectUrl);
	}

	public function testEditActionGetNoId() {
		$this->testAction('/' . $this->backendPrefix . '/languages/edit', array('method' => 'get'));

		$this->assertEqual('error', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Languages->redirectUrl);
	}

	public function testEditActionPost() {
		$langCount = $this->Languages->Language->find('count');

		$this->testAction('/' . $this->backendPrefix . '/languages/edit/1', array(
			'method' => 'post',
			'data' => array(
				'Language' => array(
					'id' => 1,
					'name' => 'English modified'
				)
			)
		));

		$this->assertEmpty($this->Languages->Language->validationErrors);
		$this->assertEqual($langCount, $this->Languages->Language->find('count'));
		$this->assertTrue($this->Languages->Language->hasAny(array('name' => 'English modified')));
		$this->assertEqual('success', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Languages->redirectUrl);
	}

	public function testEditActionPostValidationError() {
		$langCount = $this->Languages->Language->find('count');

		$this->testAction('/' . $this->backendPrefix . '/languages/edit/1', array(
			'method' => 'post',
			'data' => array(
				'Language' => array(
					'id' => 1,
					'name' => ''
				)
			)
		));

		$this->assertNotEmpty($this->Languages->Language->validationErrors);
		$this->assertEqual($langCount, $this->Languages->Language->find('count'));
		$this->assertTrue($this->Languages->Language->hasAny(array('name' => 'English')));
		$this->assertEqual('error', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertNull($this->Languages->redirectUrl);
	}

	public function testDeleteActionGetThrowsException() {
		$this->setExpectedException('MethodNotAllowedException');

		$this->testAction('/' . $this->backendPrefix . '/languages/delete', array('method' => 'get'));
	}

	public function testDeleteActionPostInvalidRequest1() {
		$langCount = $this->Languages->Language->find('count');

		$this->testAction('/' . $this->backendPrefix . '/languages/delete', array('method' => 'post'));

		$this->assertEqual($langCount, $this->Languages->Language->find('count'));
		$this->assertEqual('error', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Languages->redirectUrl);
	}

	public function testDeleteActionInvalidRequest2() {
		$langCount = $this->Languages->Language->find('count');

		$this->testAction('/' . $this->backendPrefix . '/languages/delete/1', array('method' => 'post'));

		$this->assertEqual($langCount, $this->Languages->Language->find('count'));
		$this->assertEqual('error', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Languages->redirectUrl);
	}

	public function testDeleteActionInvalidRequest3() {
		$langCount = $this->Languages->Language->find('count');

		$this->testAction('/' . $this->backendPrefix . '/languages/delete/99', array('method' => 'post'));

		$this->assertEqual($langCount, $this->Languages->Language->find('count'));
		$this->assertEqual('error', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Languages->redirectUrl);
	}

	public function testDeleteAction() {
		$this->Languages->Language->save(array(
			'Language' => array(
				'name' => 'Test Language',
				'locale' => 'tl',
				'iso' => 'tel',
				'lang' => 'te-Lg',
				'available_at_frontend' => true,
				'available_at_backend' => true,
			)
		));
		$id = $this->Languages->Language->getLastInsertID();
		$langCount = $this->Languages->Language->find('count');

		$this->testAction('/' . $this->backendPrefix . '/languages/delete/' . $id, array('method' => 'post'));

		$this->assertEqual(($langCount - 1), $this->Languages->Language->find('count'));
		$this->assertFalse($this->Languages->Language->exists($id));
		$this->assertEqual('success', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Languages->redirectUrl);
	}

	public function testSortActionThrowsExceptionOnNonAjaxRequest() {
		$this->setExpectedException('CakeException');

		$this->testAction('/' . $this->backendPrefix . '/languages/sort', array('method' => 'post'));
	}

	public function testSortActionThrowsExceptionOnAjaxGet() {
		$this->_makeAjax();

		$this->setExpectedException('CakeException');

		$this->testAction('/' . $this->backendPrefix . '/languages/sort', array('method' => 'get'));
	}

	public function testSortActionThrowsExceptionOnMissingLanguageKey() {
		$this->_makeAjax();

		$this->setExpectedException('CakeException');

		$this->testAction('/' . $this->backendPrefix . '/languages/sort', array(
			'method' => 'post',
			'data' => array(
				'Foo' => 'bar'
			)
		));
	}

	public function testSortAction() {
		$this->_makeAjax();

		$this->testAction('/' . $this->backendPrefix . '/languages/sort', array(
			'method' => 'post',
			'data' => array(
				'Language' => array(
					'1' => array(
						'id' => 1,
						'name' => 'different name',
						'position' => 2
					),
					'2' => array(
						'id' => 2,
						'position' => 1
					)
				)
			)
		));

		$this->assertEqual(2, $this->Languages->Language->field('position', array('id' => 1)));
		$this->assertEqual(1, $this->Languages->Language->field('position', array('id' => 2)));
		$this->assertFalse($this->Languages->Language->hasAny(array('name' => 'different name')));
		$this->assertArrayHasKey('status', $this->Languages->viewVars);
		$this->assertArrayHasKey('flashMessage', $this->Languages->viewVars);
		$this->assertArrayHasKey('_serialize', $this->Languages->viewVars);
		$this->assertNotEmpty($this->Languages->viewVars['_serialize']);
		$this->assertEqual('status', $this->Languages->viewVars['_serialize'][0]);
		$this->assertEqual('flashMessage', $this->Languages->viewVars['_serialize'][1]);
		$this->assertEqual('success', $this->Languages->viewVars['status']);
	}

	public function testChangeActionInvalidRequest1() {
		$this->testAction('/' . $this->backendPrefix . '/languages/change', array('method' => 'get'));

		$this->assertEqual('error', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertNotNull($this->Languages->redirectUrl);
	}

	public function testChangeActionInvalidRequest2() {
		$this->testAction('/' . $this->backendPrefix . '/languages/change/99', array('method' => 'get'));

		$this->assertEqual('error', $this->Languages->Session->read('Message.flash.params.class'));
		$this->assertNotNull($this->Languages->redirectUrl);
	}

	public function testChangeAction() {
		$this->testAction('/' . $this->backendPrefix . '/languages/change/2', array('method' => 'get'));

		$this->assertTrue($this->Languages->Session->check('Wasabi.content_language_id'));
		$this->assertEqual(2, $this->Languages->Session->read('Wasabi.content_language_id'));
		$this->assertFalse($this->Languages->Session->check('Message.flash'));
		$this->assertNotNull($this->Languages->redirectUrl);
	}

}

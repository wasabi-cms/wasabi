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

App::uses('ControllerTestCase', 'TestSuite');
App::uses('LanguagesController', 'Core.Controller');

class LanguagesTestController extends LanguagesController {

	public $redirectUrl;

	public $renderView;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	public function render($view) {
		$this->renderView = $view;
	}

}

/**
 * @property LanguagesTestController $Languages
 * @property string $backendPrefix
 */

class LanguagesControllerTest extends ControllerTestCase {

	public $fixtures = array('plugin.core.language');

	public function setUp() {
		$this->backendPrefix = Configure::read('Wasabi.backend_prefix');

		$request = new CakeRequest();
		$response = new CakeResponse();

		$this->Languages = new LanguagesTestController($request, $response);
		$this->Languages->constructClasses();
		$this->Languages->Components->init($this->Languages);
		$this->Languages->request->params['plugin'] = 'core';
		$this->Languages->request->params['controller'] = 'languages';
		$this->Languages->request->params['pass'] = array();
		$this->Languages->request->params['named'] = array();

		$this->Languages->Session->write('wasabi', array(
			'User' => array(
				'id' => 1,
				'username' => 'admin'
			)
		));

		parent::setUp();
	}

	public function testRequiredModelsAreSetup() {
		$this->assertTrue(in_array('Core.Language', $this->Languages->uses));
	}

	public function testIndexAction() {
		$this->Languages->request->params['action'] = 'index';
		$this->Languages->request->url = $this->backendPrefix . '/languages';
		$this->Languages->startupProcess();
		$this->Languages->index();

		$this->assertEmpty($this->headers);
		$this->assertNull($this->Languages->redirectUrl);
		$this->assertInternalType('array', $this->Languages->viewVars['languages']);

		$expected = array(
			array(
				'Language' => array(
					'id' => 1,
					'name' => 'English',
					'locale' => 'en',
					'iso' => 'eng',
					'lang' => 'en-US',
					'available_at_frontend' => true,
					'available_at_backend' => true,
					'position' => 1,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			),
			array(
				'Language' => array(
					'id' => 2,
					'name' => 'Deutsch',
					'locale' => 'de',
					'iso' => 'deu',
					'lang' => 'de-DE',
					'available_at_frontend' => false,
					'available_at_backend' => true,
					'position' => 2,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			)
		);
		$this->assertEqual($expected, $this->Languages->viewVars['languages']);
	}

	public function testAddActionGet() {
		$this->Languages->request->params['action'] = 'add';
		$this->Languages->request->url = $this->backendPrefix . '/languages/add';
		$this->Languages->startupProcess();
		$this->Languages->add();

		$this->assertInternalType('string', $this->Languages->viewVars['title_for_layout']);
		$this->assertNull($this->Languages->redirectUrl);
	}

	public function testAddActionPost() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('post')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'add';
		$this->Languages->request->url = $this->backendPrefix . '/languages/add';
		$this->Languages->request->data = array(
			'Language' => array(
				'name' => 'Added Language',
				'locale' => 'al',
				'iso' => 'alg',
				'lang' => 'ad-La',
				'available_at_frontend' => true,
				'available_at_backend' => true,
			)
		);

		$this->Languages->startupProcess();
		$this->Languages->add();

		$this->assertInternalType('string', $this->Languages->viewVars['title_for_layout']);
		$this->assertEmpty($this->Languages->Language->validationErrors);
		$this->assertTrue($this->Languages->Language->hasAny(array('name' => 'Added Language')));
		$this->assertTrue($this->Languages->Session->check('Message.flash'));

		$expected = 'success';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$expected = array('action' => 'index');
		$result = $this->Languages->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function testAddActionPostValidationError() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('post')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'add';
		$this->Languages->request->url = $this->backendPrefix . '/languages/add';
		$this->Languages->request->data = array(
			'Language' => array(
				'name' => '',
				'locale' => 'al',
				'iso' => 'alg',
				'lang' => 'ad-La',
				'available_at_frontend' => true,
				'available_at_backend' => true,
			)
		);

		$this->Languages->startupProcess();
		$this->Languages->add();

		$this->assertNotEmpty($this->Languages->Language->validationErrors);
		$this->assertTrue($this->Languages->Session->check('Message.flash'));

		$expected = 'error';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);
		$this->assertNull($this->Languages->redirectUrl);
	}

	public function testEditActionGet() {
		$this->Languages->request->params['action'] = 'edit';
		$this->Languages->request->url = $this->backendPrefix . '/languages/edit/1';
		$this->Languages->startupProcess();
		$this->Languages->edit(1);

		$this->assertInternalType('string', $this->Languages->viewVars['title_for_layout']);
		$this->assertNull($this->Languages->redirectUrl);

		$expected = 'add';
		$result = $this->Languages->renderView;
		$this->assertEqual($expected, $result);
	}

	public function testEditActionIdCheck1() {
		$this->Languages->request->params['action'] = 'edit';
		$this->Languages->request->url = $this->backendPrefix . '/languages/edit';
		$this->Languages->startupProcess();
		$this->Languages->edit();

		$expected = 'error';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$expected = array('action' => 'index');
		$result = $this->Languages->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function testEditActionIdCheck2() {
		$this->Languages->request->params['action'] = 'edit';
		$this->Languages->request->url = $this->backendPrefix . '/languages/edit/99';
		$this->Languages->startupProcess();
		$this->Languages->edit(99);

		$expected = 'error';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$expected = array('action' => 'index');
		$result = $this->Languages->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function testEditActionPost() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('post')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'edit';
		$this->Languages->request->url = $this->backendPrefix . '/languages/edit/1';
		$this->Languages->request->data = array(
			'Language' => array(
				'id' => 1,
				'name' => 'English edited',
			)
		);

		$this->Languages->startupProcess();
		$this->Languages->edit(1);

		$this->assertInternalType('string', $this->Languages->viewVars['title_for_layout']);
		$this->assertEmpty($this->Languages->Language->validationErrors);
		$this->assertTrue($this->Languages->Language->hasAny(array('name' => 'English edited')));
		$this->assertTrue($this->Languages->Session->check('Message.flash'));

		$expected = 'success';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$expected = array('action' => 'index');
		$result = $this->Languages->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function testEditActionPostValidationError() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('post')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'edit';
		$this->Languages->request->url = $this->backendPrefix . '/languages/edit/1';
		$this->Languages->request->data = array(
			'Language' => array(
				'id' => 1,
				'name' => '',
			)
		);

		$this->Languages->startupProcess();
		$this->Languages->edit(1);

		$this->assertNotEmpty($this->Languages->Language->validationErrors);
		$this->assertTrue($this->Languages->Session->check('Message.flash'));

		$expected = 'error';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);
		$this->assertNull($this->Languages->redirectUrl);
	}

	public function testDeleteActionThrowsExceptionOnGet() {
		$this->Languages->request->params['action'] = 'delete';
		$this->Languages->request->url = $this->backendPrefix . '/languages/delete/1';

		$this->Languages->startupProcess();

		$this->setExpectedException('MethodNotAllowedException');

		$this->Languages->delete(1);
	}

	public function testDeleteActionInvalidRequest1() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('post')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'delete';
		$this->Languages->request->url = $this->backendPrefix . '/languages/delete';

		$this->Languages->startupProcess();
		$this->Languages->delete();

		$this->assertTrue($this->Languages->Session->check('Message.flash'));

		$expected = 'error';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$expected = array('action' => 'index');
		$result = $this->Languages->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function testDeleteActionInvalidRequest2() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('post')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'delete';
		$this->Languages->request->url = $this->backendPrefix . '/languages/delete/1';

		$this->Languages->startupProcess();
		$this->Languages->delete(1);

		$this->assertTrue($this->Languages->Session->check('Message.flash'));

		$expected = 'error';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$expected = array('action' => 'index');
		$result = $this->Languages->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function testDeleteActionInvalidRequest3() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('post')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'delete';
		$this->Languages->request->url = $this->backendPrefix . '/languages/delete/99';

		$this->Languages->startupProcess();
		$this->Languages->delete(99);

		$this->assertTrue($this->Languages->Session->check('Message.flash'));

		$expected = 'error';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$expected = array('action' => 'index');
		$result = $this->Languages->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function testDeleteAction() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('post')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'delete';
		$this->Languages->request->url = $this->backendPrefix . '/languages/delete/1';

		$this->Languages->startupProcess();

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

		$lang_count = $this->Languages->Language->find('count');

		$this->Languages->delete($id);

		$expected = $lang_count - 1;
		$result = $this->Languages->Language->find('count');
		$this->assertEqual($expected, $result);
		$this->assertFalse($this->Languages->Language->exists($id));

		$this->assertTrue($this->Languages->Session->check('Message.flash'));

		$expected = 'success';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$expected = array('action' => 'index');
		$result = $this->Languages->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function testSortActionThrowsExceptionOnNonAjaxRequest() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('ajax')
			->will($this->returnValue(false));

		$this->Languages->request->params['action'] = 'sort';
		$this->Languages->request->url = $this->backendPrefix . '/languages/sort';
		$this->Languages->startupProcess();

		$this->setExpectedException('CakeException');

		$this->Languages->sort();
	}

	public function testSortActionThrowsExceptionOnEmptyData() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('ajax')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'sort';
		$this->Languages->request->url = $this->backendPrefix . '/languages/sort';
		$this->Languages->request->data = array();
		$this->Languages->startupProcess();

		$this->setExpectedException('CakeException');

		$this->Languages->sort();
	}

	public function testSortActionThrowsExceptionOnMissingLanguageKey() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('ajax')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'sort';
		$this->Languages->request->url = $this->backendPrefix . '/languages/sort';
		$this->Languages->request->data = array('Foo');
		$this->Languages->startupProcess();

		$this->setExpectedException('CakeException');

		$this->Languages->sort();
	}

	public function testSortActionThrowsExceptionOnEmptyLanguageArray() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('ajax')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'sort';
		$this->Languages->request->url = $this->backendPrefix . '/languages/sort';
		$this->Languages->request->data = array('Language' => array());
		$this->Languages->startupProcess();

		$this->setExpectedException('CakeException');

		$this->Languages->sort();
	}

	public function testSortAction() {
		$this->Languages->request = $this->getMock('CakeRequest', array('is'));
		$this->Languages->request->expects($this->once())
			->method('is')
			->with('ajax')
			->will($this->returnValue(true));

		$this->Languages->request->params['action'] = 'sort';
		$this->Languages->request->url = $this->backendPrefix . '/languages/sort';
		$this->Languages->request->data = array(
			'Language' => array(
				'1' => array(
					'id' => 1,
					'position' => 2
				),
				'2' => array(
					'id' => 2,
					'position' => 1
				)
			)
		);
		$this->Languages->startupProcess();

		$expected = 1;
		$result = $this->Languages->Language->field('position', array('id' => 1));
		$this->assertEqual($expected, $result);

		$expected = 2;
		$result = $this->Languages->Language->field('position', array('id' => 2));
		$this->assertEqual($expected, $result);

		$this->Languages->sort();

		$expected = 2;
		$result = $this->Languages->Language->field('position', array('id' => 1));
		$this->assertEqual($expected, $result);

		$expected = 1;
		$result = $this->Languages->Language->field('position', array('id' => 2));
		$this->assertEqual($expected, $result);

		$this->assertArrayHasKey('status', $this->Languages->viewVars);
		$this->assertArrayHasKey('flashMessage', $this->Languages->viewVars);

		$expected = 'success';
		$result = $this->Languages->viewVars['status'];
		$this->assertEqual($expected, $result);

		$this->assertArrayHasKey('_serialize', $this->Languages->viewVars);
		$this->assertNotEmpty($this->Languages->viewVars['_serialize']);

		$expected = 'status';
		$result = $this->Languages->viewVars['_serialize'][0];
		$this->assertEqual($expected, $result);

		$expected = 'flashMessage';
		$result = $this->Languages->viewVars['_serialize'][1];
		$this->assertEqual($expected, $result);
	}

	public function testChangeActionInvalidRequest() {
		$this->Languages->request->params['action'] = 'change';
		$this->Languages->request->url = $this->backendPrefix . '/languages/change/99';

		$this->Languages->startupProcess();
		$this->Languages->change(99);

		$this->assertTrue($this->Languages->Session->check('Message.flash'));

		$expected = 'error';
		$result = $this->Languages->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$this->assertNotNull($this->Languages->redirectUrl);
	}

	public function testChangeAction() {
		$this->Languages->request->params['action'] = 'change';
		$this->Languages->request->url = $this->backendPrefix . '/languages/change/2';

		$this->Languages->startupProcess();
		$this->Languages->change(2);

		$this->assertTrue($this->Languages->Session->check('Wasabi.content_language_id'));

		$expected = 2;
		$result = $this->Languages->Session->read('Wasabi.content_language_id');
		$this->assertEqual($expected, $result);

		$this->assertNotNull($this->Languages->redirectUrl);
	}

	public function tearDown() {
		$this->Languages->Session->delete('wasabi');
		unset($this->Languages, $this->backendPrefix);

		parent::tearDown();
	}

}

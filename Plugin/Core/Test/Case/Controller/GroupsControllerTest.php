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
App::uses('GroupsController', 'Core.Controller');

class GroupsTestController extends GroupsController {

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
 * @property GroupsTestController $Groups
 */

class GroupsControllerTest extends CoreControllerTest {

	public $fixtures = array('plugin.core.group', 'plugin.core.user');

	public function setUp() {
		$this->Groups = $this->generate('GroupsTest');
		$this->_loginUser();

		parent::setUp();
	}

	public function tearDown() {
		unset($this->Groups);

		parent::tearDown();
	}

	public function testRequiredModelsAreSetup() {
		$this->assertTrue(in_array('Core.Group', $this->Groups->uses));
	}

	public function testIndexAction() {
		$this->testAction('/' . $this->backendPrefix . '/groups', array('method' => 'get'));

		$this->assertInternalType('string', $this->Groups->viewVars['title_for_layout']);
		$this->assertTrue(isset($this->Groups->viewVars['groups']));
		$this->assertNotEmpty($this->Groups->viewVars['groups']);
	}

	public function testAddActionGet() {
		$this->testAction('/' . $this->backendPrefix . '/groups/add', array('method' => 'get'));

		$this->assertInternalType('string', $this->Groups->viewVars['title_for_layout']);
		$this->assertNull($this->Groups->redirectUrl);
	}

	public function testAddActionPost() {
		$grp_count = $this->Groups->Group->find('count');

		$this->testAction('/' . $this->backendPrefix . '/groups/add', array(
			'method' => 'post',
			'data' => array(
				'Group' => array(
					'name' => 'Added Group'
				)
			)
		));

		$this->assertEmpty($this->Groups->Group->validationErrors);
		$this->assertEqual(($grp_count + 1), $this->Groups->Group->find('count'));
		$this->assertTrue($this->Groups->Group->hasAny(array('name' => 'Added Group')));
		$this->assertEqual('success', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Groups->redirectUrl);
	}

	public function testAddActionPostValidationError() {
		$grp_count = $this->Groups->Group->find('count');

		$this->testAction('/' . $this->backendPrefix . '/groups/add', array(
			'method' => 'post',
			'data' => array(
				'Group' => array(
					'name' => ''
				)
			)
		));

		$this->assertNotEmpty($this->Groups->Group->validationErrors);
		$this->assertEqual($grp_count, $this->Groups->Group->find('count'));
		$this->assertEqual('error', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertNull($this->Groups->redirectUrl);
	}

	public function testEditActionGet() {
		$this->testAction('/' . $this->backendPrefix . '/groups/edit/1', array('method' => 'get'));

		$this->assertInternalType('string', $this->Groups->viewVars['title_for_layout']);
		$this->assertNull($this->Groups->redirectUrl);
		$this->assertEqual('add', $this->Groups->renderView);

		$expected = $this->Groups->Group->findById(1);
		$result = $this->Groups->request->data;
		$this->assertEqual($expected, $result);
	}

	public function testEditActionGetNonExistentId() {
		$this->testAction('/' . $this->backendPrefix . '/groups/edit/99', array('method' => 'get'));

		$this->assertEqual('error', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Groups->redirectUrl);
	}

	public function testEditActionGetNoId() {
		$this->testAction('/' . $this->backendPrefix . '/groups/edit', array('method' => 'get'));

		$this->assertEqual('error', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Groups->redirectUrl);
	}

	public function testEditActionPost() {
		$grp_count = $this->Groups->Group->find('count');

		$this->testAction('/' . $this->backendPrefix . '/groups/edit/1', array(
			'method' => 'post',
			'data' => array(
				'Group' => array(
					'id' => 1,
					'name' => 'Administrator modified'
				)
			)
		));

		$this->assertEmpty($this->Groups->Group->validationErrors);
		$this->assertEqual($grp_count, $this->Groups->Group->find('count'));
		$this->assertTrue($this->Groups->Group->hasAny(array('name' => 'Administrator modified')));
		$this->assertEqual('success', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Groups->redirectUrl);
	}

	public function testEditActionPostValidationError() {
		$grp_count = $this->Groups->Group->find('count');

		$this->testAction('/' . $this->backendPrefix . '/groups/edit/1', array(
			'method' => 'post',
			'data' => array(
				'Group' => array(
					'id' => 1,
					'name' => ''
				)
			)
		));

		$this->assertNotEmpty($this->Groups->Group->validationErrors);
		$this->assertEqual($grp_count, $this->Groups->Group->find('count'));
		$this->assertTrue($this->Groups->Group->hasAny(array('name' => 'Administrator')));
		$this->assertEqual('error', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertNull($this->Groups->redirectUrl);
	}

	public function testDeleteActionGetThrowsException() {
		$this->setExpectedException('MethodNotAllowedException');

		$this->testAction('/' . $this->backendPrefix . '/groups/delete', array('method' => 'get'));
	}

	public function testDeleteActionPostNoId() {
		$this->testAction('/' . $this->backendPrefix . '/groups/delete', array('method' => 'post'));

		$this->assertEqual('error', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Groups->redirectUrl);
	}

	public function testDeleteActionPostInvalidId() {
		$this->testAction('/' . $this->backendPrefix . '/groups/delete/1', array('method' => 'post'));

		$this->assertEqual('error', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Groups->redirectUrl);
	}

	public function testDeleteActionPostNonExistentId() {
		$this->testAction('/' . $this->backendPrefix . '/groups/delete/99', array('method' => 'post'));

		$this->assertEqual('error', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Groups->redirectUrl);
	}

	public function testDeleteActionPostEmptyGroup() {
		$this->testAction('/' . $this->backendPrefix . '/groups/delete/3', array('method' => 'post'));

		$this->assertEqual('success', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Groups->redirectUrl);
	}

	public function testDeleteActionPostNotEmptyGroup() {
		$this->testAction('/' . $this->backendPrefix . '/groups/delete/2', array('method' => 'post'));

		$this->assertArrayHasKey('group', $this->Groups->viewVars);
		$this->assertArrayHasKey('groups', $this->Groups->viewVars);
		$this->assertNull($this->Groups->redirectUrl);
	}

	public function testDeleteActionPostNotEmptyGroupWithAlternativeIdSet() {
		$this->testAction('/' . $this->backendPrefix . '/groups/delete/2', array(
			'method' => 'post',
			'data' => array(
				'Group' => array(
					'alternative_group_id' => 3
				)
			)
		));

		$this->assertEqual('success', $this->Groups->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Groups->redirectUrl);
		$this->assertEmpty($this->Groups->Group->findById(2));
		$this->assertEqual(1, $this->Groups->Group->field('user_count', array('id' => 3)));
	}

}

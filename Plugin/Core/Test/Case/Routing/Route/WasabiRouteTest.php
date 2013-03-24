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
 * @origin        http://github.com/frankfoerster/LessMin
 * @package       LessMin
 * @subpackage    LessMin.Test.Case.Routing.Filter
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeTestCase', 'TestSuite');
App::uses('WasabiRoute', 'Core.Routing/Route');

class TestWasabiRoute extends WasabiRoute {

	public $stopped = false;
	public $responseSent = false;

	public function sendResponse() {
		$this->_sendResponse();
	}

	public function sendResponse2() {
		$this->_sendResponse2();
	}

	protected function _sendResponse() {
		$this->responseSent = true;
	}

	protected function _sendResponse2() {
		parent::_sendResponse();
		$this->responseSent = true;
	}

	protected function _stop($code = 0) {
		$this->stopped = true;
	}

}

/**
 * @property TestWasabiRoute $WasabiRoute
 */

class WasabiRouteTest extends CakeTestCase {

	public $fixtures = array('plugin.core.route');

	public function setUp() {
		$this->WasabiRoute = new TestWasabiRoute('/');

		parent::setUp();
	}

	public function tearDown() {
		unset($this->WasabiRoute);
		$this->_clearCache();

		parent::tearDown();
	}

	public function testMatch() {
		$result = $this->WasabiRoute->match('/foo');
		$this->assertFalse($result);

		$result = $this->WasabiRoute->match(array('controller' => 'foo', 'action' => 'bar'));
		$this->assertFalse($result);

		$result = $this->WasabiRoute->match(array('plugin' => false, 'controller' => 'foo', 'action' => 'bar'));
		$this->assertFalse($result);
	}

	public function testMatchCached() {
		$url = array('plugin' => null, 'controller' => 'foo_test',	'action' => 'bar');
		$identifier = md5(serialize($url));
		Cache::write($identifier, '/foo_test/bar', 'core.routes');

		$result = $this->WasabiRoute->match($url);
		$this->assertEqual('/foo_test/bar', $result);
	}

	public function testMatchFound() {
		$url = array('controller' => 'tests', 'action' => 'foo');
		$result = $this->WasabiRoute->match($url);
		$this->assertEqual('/tests/foo', $result);
	}

	public function testMatchWithParams() {
		$url = array('controller' => 'tests', 'action' => 'foo', 3);
		$result = $this->WasabiRoute->match($url);
		$this->assertEqual('/tests/foo/3', $result);

		$url = array('controller' => 'tests', 'action' => 'fool', 3);
		$result = $this->WasabiRoute->match($url);
		$this->assertFalse($result);

		$url = array('controller' => 'tests', 'action' => 'foo', 3, 'bar' => 'test');
		$result = $this->WasabiRoute->match($url);
		$this->assertEqual('/tests/foo/3/bar:test', $result);

		$url = array('controller' => 'tests', 'action' => 'fool', 3, 'bar' => 'test');
		$result = $this->WasabiRoute->match($url);
		$this->assertFalse($result);
	}

	public function testParseRootOrWithoutUrl() {
		$expected = array(
			'plugin' => 'awesome',
			'controller' => 'tests',
			'action' => 'foo',
			'pass' => array(3),
			'named' => array()
		);
		$result = $this->WasabiRoute->parse('');
		$this->assertEqual($expected, $result);

		$result = $this->WasabiRoute->parse('/');
		$this->assertEqual($expected, $result);
	}

	public function testParseNonExistentUrl() {
		$result = $this->WasabiRoute->parse('/asfdjkhlashfl/lsahfuhasjkf');
		$this->assertFalse($result);
	}

	public function testParseRouteWithNamedParams() {
		$expected = array(
			'plugin' => null,
			'controller' => 'tests',
			'action' => 'foo',
			'pass' => array(3),
			'named' => array(
				'bar' => 'test'
			)
		);
		$result = $this->WasabiRoute->parse('/tests/foo/3/bar:test');
		$this->assertEqual($expected, $result);
	}

	public function testParseRedirectedUrlWithCustomStatusCode() {
		$this->WasabiRoute->parse('/awesome/tests/foo/4');
		$this->assertTrue($this->WasabiRoute->responseSent);

		$header = $this->WasabiRoute->response->header();
		$this->assertTrue(isset($header['Location']));
		$this->assertInternalType('string', $header['Location']);
		$this->assertContains('http://', $header['Location']);
		$this->assertEqual(307, $this->WasabiRoute->response->statusCode());
		$this->assertTrue($this->WasabiRoute->responseSent);
	}

	public function testParseRedirectedUrlWithDefaultStatusCode() {
		$this->WasabiRoute->parse('/awesome/tests/foo/5');
		$this->assertTrue($this->WasabiRoute->responseSent);

		$header = $this->WasabiRoute->response->header();
		$this->assertTrue(isset($header['Location']));
		$this->assertInternalType('string', $header['Location']);
		$this->assertContains('http://', $header['Location']);
		$this->assertEqual(301, $this->WasabiRoute->response->statusCode());
		$this->assertTrue($this->WasabiRoute->responseSent);
	}

	public function testSendResponse() {
		$this->WasabiRoute->response = $this->getMock('CakeResponse', array('send'));
		$this->WasabiRoute->response
			->expects($this->once())
			->method('send');
		$this->WasabiRoute->sendResponse2();
		$this->assertTrue($this->WasabiRoute->responseSent);
	}

	protected function _clearCache() {
		$cache = Cache::config('core.routes');
		$cache_folder = new Folder($cache['settings']['path']);
		$cache_folder->delete();

		unset($cache, $cache_folder);
	}

}

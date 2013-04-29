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

App::uses('CakeEvent', 'Event');
App::uses('CakePlugin', 'Core');
App::uses('CakeTestCase', 'TestSuite');
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('LessMinProcessor', 'Core.Routing/Filter');

class LessMinProcessorTest extends CakeTestCase {

	/**
	 * Holds the webroot of the test_app
	 *
	 * @var string
	 */
	protected $_testAppWebroot;

	/**
	 * Setup the webroot of the test_app.
	 *
	 * @return void
	 */
	public function setUp() {
		$this->_testAppWebroot = CakePlugin::path('Core') . 'Test' . DS . 'test_app' . DS . 'webroot' . DS;

		parent::setUp();
	}

	/**
	 * Delete css files/folders that have been created during the tests.
	 *
	 * @return void
	 */
	public function tearDown() {
		$cssFolder = new Folder($this->_testAppWebroot . 'css');
		$cssFolder->delete();

		foreach (CakePlugin::loaded() as $p) {
			$cssFolder = new Folder(CakePlugin::path($p) . 'webroot' . DS . 'css' . DS);
			$cssFolder->delete();
		}

		parent::tearDown();
	}

	/**
	 * Test the functionality of LessMinFilter::processLessFiles
	 *
	 * @covers LessMinProcessor::processLessFiles
	 * @return void
	 */
	public function testProcessLessFiles() {
		$filter = new LessMinProcessor();
		$cssFolder = $this->_testAppWebroot . 'css' . DS;

		// css folder does not exist first hand
		$this->assertFalse(is_dir($cssFolder));

		// process less files
		$filter->processLessFiles(new Folder($this->_testAppWebroot . 'less' . DS, false), $this->_testAppWebroot);

		// css folder should be there now
		$this->assertTrue(is_dir($cssFolder));

		// and the compiled css file should be present
		$cssFile = $cssFolder . 'style.css';
		$this->assertTrue(file_exists($cssFile));

		// check that the less file is correctly compiled to css and minified
		$expected = 'body{color:#333;padding:0;margin:0}';
		$result = file_get_contents($cssFile);
		$this->assertEqual($expected, $result);
	}

	/**
	 * Test Less compilation and minification for plugins.
	 *
	 * @covers LessMinProcessor::beforeDispatch
	 * @return void
	 */
	public function testPluginCompilation() {
		$filter = new LessMinProcessor();
		$request = new CakeRequest('/');
		$response = $this->getMock('CakeResponse');
		App::build(array(
			'Plugin' => array(CakePlugin::path('Core') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS),
		), APP::RESET);

		CakePlugin::load('TestPlugin');
		$pluginWebroot = CakePlugin::path('TestPlugin') . 'webroot' . DS;
		$cssFolder = $pluginWebroot . 'css' . DS;

		// css folder does not exist first hand
		$this->assertFalse(is_dir($cssFolder));

		$event = new CakeEvent('DispatcherTest', $this, compact('request', 'response'));
		$filter->beforeDispatch($event);

		// css folder should be there now
		$this->assertTrue(is_dir($cssFolder));

		// and the compiled css file should be present
		$cssFile = $cssFolder . 'style.css';
		$this->assertTrue(file_exists($cssFile));

		// check that the less file is correctly compiled to css and minified
		$expected = 'body{color:#333;padding:0;margin:0}';
		$result = file_get_contents($cssFile);
		$this->assertEqual($expected, $result);

		CakePlugin::unload('TestPlugin');
	}

	/**
	 * Test 'LessMin.SKIP_ON_PRODUCTION' setting
	 *
	 * @covers LessMinProcessor::beforeDispatch
	 * @return void
	 */
	public function testSkipOnProduction() {
		$filter = new LessMinProcessor();
		$request = new CakeRequest('/');
		$response = $this->getMock('CakeResponse');

		// check 'SKIP_ON_PRODUCTION' setting
		$oldDebugLvl = Configure::read('debug');
		Configure::write('LessMin.SKIP_ON_PRODUCTION', true);
		Configure::write('debug', 0);
		$event = new CakeEvent('DispatcherTest', $this, compact('request', 'response'));
		$this->assertEqual('skipped', $filter->beforeDispatch($event));

		Configure::write('debug', 2);
		$event = new CakeEvent('DispatcherTest', $this, compact('request', 'response'));
		$this->assertNull($filter->beforeDispatch($event));

		Configure::write('debug', $oldDebugLvl);
	}

}

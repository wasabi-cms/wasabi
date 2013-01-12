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
	protected $testAppWebroot;

/**
 * Setup the webroot of the test_app.
 *
 * @return void
 */
	public function setUp() {
		$this->testAppWebroot = CakePlugin::path('Core') . 'Test' . DS . 'test_app' . DS . 'webroot' . DS;
	}

/**
 * Delete css files/folders that have been created during the tests.
 *
 * @return void
 */
	public function tearDown() {
		$css_folder = new Folder($this->testAppWebroot . 'css');
		$css_folder->delete();

		foreach (CakePlugin::loaded() as $p) {
			$css_folder = new Folder(CakePlugin::path($p) . 'webroot' . DS . 'css' . DS);
			$css_folder->delete();
		}
	}

/**
 * Test the functionality of LessMinFilter::processLessFiles
 *
 * @covers LessMinProcessor::processLessFiles
 * @return void
 */
	public function testProcessLessFiles() {
		$filter = new LessMinProcessor();
		$css_folder = $this->testAppWebroot . 'css' . DS;

		// css folder does not exist first hand
		$this->assertFalse(is_dir($css_folder));

		// process less files
		$filter->processLessFiles(new Folder($this->testAppWebroot . 'less' . DS, false), $this->testAppWebroot);

		// css folder should be there now
		$this->assertTrue(is_dir($css_folder));

		// and the compiled css file should be present
		$css_file = $css_folder . 'style.css';
		$this->assertTrue(file_exists($css_file));

		// check that the less file is correctly compiled to css and minified
		$expected = 'body{color:#333;padding:0;margin:0}';
		$result = file_get_contents($css_file);
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
		$plugin_webroot = CakePlugin::path('TestPlugin') . 'webroot' . DS;
		$css_folder = $plugin_webroot . 'css' . DS;

		// css folder does not exist first hand
		$this->assertFalse(is_dir($css_folder));

		$event = new CakeEvent('DispatcherTest', $this, compact('request', 'response'));
		$filter->beforeDispatch($event);

		// css folder should be there now
		$this->assertTrue(is_dir($css_folder));

		// and the compiled css file should be present
		$css_file = $css_folder . 'style.css';
		$this->assertTrue(file_exists($css_file));

		// check that the less file is correctly compiled to css and minified
		$expected = 'body{color:#333;padding:0;margin:0}';
		$result = file_get_contents($css_file);
		$this->assertEqual($expected, $result);
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
		$old_debug_lvl = Configure::read('debug');
		Configure::write('LessMin.SKIP_ON_PRODUCTION', true);
		Configure::write('debug', 0);
		$event = new CakeEvent('DispatcherTest', $this, compact('request', 'response'));
		$this->assertEqual('skipped', $filter->beforeDispatch($event));

		Configure::write('debug', 2);
		$event = new CakeEvent('DispatcherTest', $this, compact('request', 'response'));
		$this->assertNull($filter->beforeDispatch($event));

		Configure::write('debug', $old_debug_lvl);
	}

}

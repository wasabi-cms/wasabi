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
 * @subpackage    Wasabi.Test.Case
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakePlugin', 'Core');
App::uses('CakeTestSuite', 'TestSuite');

class AllPluginTestsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Plugin Tests Test');
		$plugins = CakePlugin::loaded();
		foreach ($plugins as $plugin) {
			$file = CakePlugin::path($plugin) . 'Test' . DS . 'Case' . DS . 'All' . $plugin . 'TestsTest.php';
			if (file_exists($file)) {
				$suite->addTestFile($file);
			}
		}
		return $suite;
	}

}

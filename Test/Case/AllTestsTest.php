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

class AllTestsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Add all wasabi related tests to the suite
	 *
	 * @return PHPUnit_Framework_TestSuite
	 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('All Tests');
		$path = dirname(__FILE__) . DS;
		$suite->addTestFile($path . 'AllPluginTestsTest.php');
		return $suite;
	}

}

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
 * @subpackage    Wasabi.Plugin.Core.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Setting', 'Core.Model');

class CoreMediaSetting extends Setting {

	/**
	 * This model uses the 'settings' db table.
	 *
	 * @var string
	 */
	public $useTable = 'settings';

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(

	);
}

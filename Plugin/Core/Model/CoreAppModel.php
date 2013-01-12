<?php
/**
 * Wasabi wide base model
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

App::uses('AppModel', 'Model');

class CoreAppModel extends AppModel {

	/**
	 * Always attach these behaviors
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Containable'
	);

	/**
	 * Recursive level
	 * -1: do not autobind any associations on find() / read() calls
	 *
	 * @var int
	 */
	public $recursive = -1;

}

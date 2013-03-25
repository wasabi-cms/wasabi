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

App::uses('CoreAppModel', 'Core.Model');

class CoreInstall extends CoreAppModel {

	/**
	 * This model is tableless.
	 *
	 * @var bool|string
	 */
	public $useTable = false;

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'host' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a valid host. (e.g. localhost)'
			)
		),
		'login' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a valid login or username.'
			)
		),
		'database' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter the name of your database.'
			)
		),
		'port' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please enter a valid port number.',
				'allowEmpty' => true
			)
		),
		'cookie_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a name for your Cookie.'
			)
		)
	);

}

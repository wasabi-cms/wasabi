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
 * @subpackage    Wasabi.Plugin.Cms.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CoreAppModel', 'Core.Model');

class CmsPageModule extends CoreAppModel {

	/**
	 * Attached Behaviors
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Core.Translatable' => array(
			'fields' => array(
				'variables',
				'is_online'
			)
		)
	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'CmsPage' => array(
			'className' => 'Cms.CmsPage'
		)
	);

}

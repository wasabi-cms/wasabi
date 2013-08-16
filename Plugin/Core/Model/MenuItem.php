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
App::uses('Hash', 'Utility');

class MenuItem extends CoreAppModel {

	const TYPE_EXTERNAL_LINK = 'ExternalLink';
	const TYPE_OBJECT = 'Object';
	const TYPE_ACTION = 'Action';
	const TYPE_CUSTOM_ACTION = 'CustomAction';

	public $actsAs = array(
		'Core.EnhancedTree',
		'Core.Translatable' => array(
			'fields' => array(
				'name'
			)
		)
	);

	public $order = 'MenuItem.lft ASC';

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Menu' => array(
			'className' => 'Core.Menu',
			'counterCache' => true
		)
	);

	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a name for the menu item.'
			)
		),
		'external_link' => array(
			'isValid' => array(
				'rule' => 'url',
				'message' => 'Please enter a valid external link.'
			)
		),
		'controller' => array(
			'isValid' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a valid controller name.'
			)
		),
		'action' => array(
			'isValid' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a valid action name.'
			)
		)
	);

	public static function instance() {
		static $instance;
		if (!$instance) {
			$instance = ClassRegistry::init('Core.MenuItem');
		}
		return $instance;
	}

	/**
	 * Find all menu items with find $options
	 *
	 * @param array $options
	 * @return array
	 */
	public function findAll($options = array()) {
		return $this->find('all', $options);
	}

	public function validateExternalLink($content) {
		var_dump($content);
		die();
	}

}

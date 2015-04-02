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

class CmsContentArea extends CoreAppModel {

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'CmsLayout' => array(
			'className' => 'Cms.CmsLayout'
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'CmsPagesModule' => array(
			'className' => 'Cms.CmsPagesModule'
		)
	);

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a name for this content area.'
			),
			'unique' => array(
				'rule' => array('isUniqueForLayout', 'name'),
				'message' => 'This name is already used by another content area.'
			)
		)
	);

	/**
	 * isUniqueForLayout custom validation rule
	 *
	 * @param array $content
	 * @param string $column
	 * @return boolean
	 */
	public function isUniqueForLayout($content, $column) {
		$options = array(
			'conditions' => array(
				$column => $content[$column]
			)
		);
		if (isset($this->data[$this->alias]['id']) && $this->data[$this->alias]['id'] !== '') {
			$options['conditions']['id <>'] = $this->data[$this->alias]['id'];
		}
		return !$this->find('count', $options);
	}

}

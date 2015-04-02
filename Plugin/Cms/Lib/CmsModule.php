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
 * @subpackage    Wasabi.Plugin.Cms.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

abstract class CmsModule {

	/**
	 * Id of the module.
	 *
	 * @var string
	 */
	protected $_id;

	/**
	 * Name (translated) of the module.
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * @var string
	 */
	protected $_modulePath = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$obj = new ReflectionClass($this);
		$this->_modulePath = pathinfo($obj->getFileName())['dirname'];
		$this->_id = $this->_extractId();
	}

	/**
	 * Get the name of the module.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Get the id of the module.
	 *
	 * @return string
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * Get the full path containing the input.ctp and output.ctp templates.
	 *
	 * @return string
	 */
	public function getModulePath() {
		return $this->_modulePath;
	}

	protected function _extractId() {
		$className = get_class($this);
		$id = explode('CmsModule', $className);
		if (!$id || $id[0] === '') {
			user_error(__d('cms', 'The CMS Module class %s has an invalid name. It must end with "...CmsModule".', array($className)));
		}
		return $id[0];
	}

}

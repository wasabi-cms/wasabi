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

abstract class CmsLayout {

	/**
	 * Id of the layout.
	 *
	 * @var string
	 */
	protected $_id;

	/**
	 * Name (translated) of the layout.
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * Layout attributes of the layout.
	 *
	 * @var array
	 */
	protected $_attributes = array();

	/**
	 * Content areas of the layout.
	 *
	 * @var array
	 */
	protected $_contentAreas = array();

	/**
	 * @var string
	 */
	protected $_layoutPath = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$obj = new ReflectionClass($this);
		$this->_layoutPath = pathinfo($obj->getFileName())['dirname'];
		$this->_id = $this->_extractId();
	}

	/**
	 * Get all layout attributes of the layout.
	 *
	 * @return array
	 */
	public function getAttributes() {
		return $this->_attributes;
	}

	/**
	 * Get all content areas of the layout.
	 *
	 * @return array
	 */
	public function getContentAreas() {
		return $this->_contentAreas;
	}

	/**
	 * Get the name of the layout.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Get the id of the layout.
	 *
	 * @return string
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * Get the full path containing the layout.ctp template.
	 *
	 * @return string
	 */
	public function getLayoutPath() {
		return $this->_layoutPath;
	}

	/**
	 * Extract the lower cased id from the layout class name.
	 *
	 * @return string
	 */
	protected function _extractId() {
		$className = get_class($this);
		$id = explode('CmsLayout', $className);
		if (!$id || $id[0] === '') {
			user_error(__d('cms', 'The layout Class %s has an invalid name. The name has to end with CmsLayout.'));
		}
		return strtolower($id[0]);
	}

}

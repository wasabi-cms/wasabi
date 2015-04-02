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
 * @subpackage    Wasabi.Plugin.Core.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');

/**
 * @property FormHelper $Form
 */

class BulkHelper extends AppHelper {

	/**
	 * Helpers used by this helper.
	 *
	 * @var array
	 */
	public $helpers = array(
		'Form'
	);

	protected $_actions = array();

	protected $_target;

	public function addActions($actions = array()) {
		$this->_actions = array_merge($this->_actions, $actions);
	}

	public function resetActions() {
		$this->_actions = array();
	}

	public function setTarget($target) {
		$this->_target = $target;
	}

	public function render($class) {
		if (empty($this->_actions) || !isset($this->_target)) {
			return '';
		}
		$classes = array('bulk');
		if (is_string($class)) {
			$class = array($class);
		}
		$classes = array_merge($classes, $class);

		$out = $this->Form->create(null, array('id' => false, 'class' => join(' ', $classes), 'data-target' => $this->_target))
			 . $this->Form->input('action', array('label' => false, 'div' => false, 'options' => $this->_actions, 'class' => 'select-small', 'empty' => __d('core', 'Bulk Actions')))
			 . $this->Form->button(__d('core', 'Apply'), array('class' => 'button small blue'))
			 . $this->Form->end();

		return $out;
	}
}
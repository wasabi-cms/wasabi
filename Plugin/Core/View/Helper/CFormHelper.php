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

class CFormHelper extends AppHelper {

	/**
	 * Holds the model fields and their validators.
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Helpers used by this helper.
	 *
	 * @var array
	 */
	public $helpers = array(
		'Form'
	);

	/**
	 * Creates an input/textarea/checkbox/select element
	 * with custom formatting for backend wide usage.
	 *
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function input($field, $options) {
		$class = '';
		$out = '<div class="form-row{CLASS}">{LABEL}<div class="field">{INPUT}{ERROR}{INFO}</div></div>';
		if (isset($options['type']) && $options['type'] == 'checkbox') {
			$class .= ' checkbox';
			$out = '<div class="form-row{CLASS}">{TITLE}<div class="field">{INPUT}{LABEL}{ERROR}{INFO}</div></div>';
		}
		$title = (isset($options['title'])) ? '<label>'.$options['title'].'</label>' : '';
		if (isset($options['label_info'])) {
			$options['label'] .= '<small>'. $options['label_info'] .'</small>';
			unset($options['label_info']);
		}
		$label = $this->Form->label($field, $options['label']);
		$options['label'] = false;
		$options['div'] = false;
		$options['format'] = array('input');
		$error = $this->Form->tagIsInvalid();
		if ($error) {
			$class .= ' error';
			$error = '<span class="error-message">'. $error[0] .'</span>';
		}
		$field_options = $options;
		unset($field_options['info']);
		$input = $this->Form->input($field, $field_options);
		if (!isset($this->fields[$this->Form->model()])) {
			$this->fields[$this->Form->model()] = ClassRegistry::init($this->Form->model())->validate;
		}
		$required = isset($this->fields[$this->Form->model()][$this->Form->field()]);
		if ($required) {
			$class .= ' required';
		}
		$info = '';
		if (isset($options['info'])) {
			$info .= '<small>'. $options['info']. '</small>';
			unset($options['info']);
		}
		$out = str_replace(
			array('{CLASS}', '{TITLE}', '{LABEL}', '{INPUT}', '{ERROR}', '{INFO}'),
			array($class, $title, $label, $input, $error, $info),
			$out
		);
		return $out;
	}

}
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
 * @subpackage    Wasabi.Plugin.Core.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class WasabiEvent {

	/**
	 * Name of the event
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The subject on which the event is called on
	 *
	 * @var object
	 */
	public $subject;

	/**
	 * Data that has been supplied on the event trigger
	 *
	 * @var mixed
	 */
	public $data;

	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param object $subject
	 * @param mixed $data
	 */
	public function __construct($name, &$subject, $data) {
		$this->name = $name;
		$this->subject = $subject;
		$this->data = $data;
	}

}

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

class Plugin extends CoreAppModel {

	public function findAllInstalled() {
		return $this->find('all', array(
			'conditions' => array(
				$this->alias . '.installed' => true
			),
			'order' => $this->alias . '.name ASC'
		));
	}

}

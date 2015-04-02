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

App::uses('Cache', 'Cache');
App::uses('CakeSession', 'Model/Datasource');

class Collection extends CoreAppModel {

	public $belongsTo = array(
		'CmsPage' => array(
			'className' => 'Cms.CmsPage'
		)
	);

}

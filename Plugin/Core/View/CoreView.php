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
 * @subpackage    Wasabi.Plugin.Core.View
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('View', 'View');

/**
 * @property CHtmlHelper $CHtml
 * @property CFormHelper $CForm
 * @property NavigationHelper $Navigation
 * @property MenuHelper $Menu
 * @property WasabiAssetHelper $WasabiAsset
 * @property array $data shorthand for $this->request->data
 * @property array $params shorthand for $this->request->params
 */

class CoreView extends View {

	public $backendPrefix;

	public function __construct(Controller $controller = null) {
		parent::__construct($controller);

		$this->backendPrefix = Configure::read('Wasabi.backend_prefix');
	}

}

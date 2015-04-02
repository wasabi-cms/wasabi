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
 * @property CoreHtmlHelper $Html
 * @property CFormHelper $CForm
 * @property NavigationHelper $Navigation
 * @property CMenuHelper $CMenu
 * @property WasabiAssetHelper $WasabiAsset
 * @property ImageHelper $Image
 * @property FilterHelper $Filter
 * @property BulkHelper $Bulk
 * @property MenuHelper $Menu
 * @property array $data shorthand for $this->request->data
 * @property array $params shorthand for $this->request->params
 * @property array $activeFilters
 * @property array $filterFields
 * @property array $activeSort
 * @property array $sortFields
 * @property array $paginationParams
 * @property array $defaultSort
 */

class CoreView extends View {

	public $backendPrefix;

	public $activeFilters;
	public $filterFields;
	public $activeSort;
	public $sortFields;

	public function __construct(Controller $controller = null) {
		parent::__construct($controller);

		$this->backendPrefix = Configure::read('Wasabi.backend_prefix');

		if (isset($controller->Filter)) {
			$this->activeFilters = $controller->Filter->activeFilters;
			$this->filterFields = $controller->Filter->filterFields;
			$this->activeSort = $controller->Filter->activeSort;
			$this->sortFields = $controller->Filter->sortFields;
			$this->paginationParams = $controller->Filter->paginationParams;
			$this->defaultSort = $controller->Filter->defaultSort;
		}
	}

}

<?php

App::uses('CmsModule', 'Cms.Model');

class TextileModule extends CmsModule {

	public function __construct() {

		$this->_name = __d('cms_modules', 'Text (Textile)');

		parent::__construct();
	}

}

<?php

App::uses('CmsLayout', 'Cms.Lib');
App::uses('CmsLayoutAttributeType', 'Cms.Lib');

class DefaultCmsLayout extends CmsLayout {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->_name = __d('layouts', 'Default');

		$this->_attributes = array(
			'body_css_class' => array(
				'name' => __d('layouts', 'Body CSS Class'),
				'type' => CmsLayoutAttributeType::TYPE_TEXT
			),
			'og:title' => array(
				'name' => __d('layouts', 'Open Graph Title'),
				'type' => CmsLayoutAttributeType::TYPE_TEXT
			),
			'og:description' => array(
				'name' => __d('layouts', 'Open Graph Description'),
				'type' => CmsLayoutAttributeType::TYPE_TEXTAREA
			),
			'og:type' => array(
				'name' => __d('layouts', 'Open Graph Type'),
				'type' => CmsLayoutAttributeType::TYPE_SELECT,
				'options' => array(
					'article' => __d('layouts', 'Article'),
					'website' => __d('layouts', 'Website')
				),
				'empty' => true
			),
			'og:image' => array(
				'name' => __d('layouts', 'Open Graph Image'),
				'type' => CmsLayoutAttributeType::TYPE_IMAGE
			)
		);

		$this->_contentAreas = array(
			'main' => __d('layouts', 'Main'),
			'sidebar' => __d('layouts', 'Sidebar'),
			'third' => __d('layouts', 'Third Content Area')
		);

		parent::__construct();
	}

}

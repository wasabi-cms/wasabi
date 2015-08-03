<?php

namespace App\View\Layout\Cms\DefaultLayout;

use Wasabi\Cms\View\Layout\Layout;

class DefaultLayout extends Layout {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->_name = __d('layouts', 'Default');

		$this->_attributes = [
			'body_css_class' => [
				'name' => __d('layouts', 'Body CSS Class'),
//				'type' => CmsLayoutAttributeType::TYPE_TEXT
			],
			'og:title' => [
				'name' => __d('layouts', 'Open Graph Title'),
//				'type' => CmsLayoutAttributeType::TYPE_TEXT
			],
			'og:description' => [
				'name' => __d('layouts', 'Open Graph Description'),
//				'type' => CmsLayoutAttributeType::TYPE_TEXTAREA
			],
			'og:type' => [
				'name' => __d('layouts', 'Open Graph Type'),
//				'type' => CmsLayoutAttributeType::TYPE_SELECT,
				'options' => array(
					'article' => __d('layouts', 'Article'),
					'website' => __d('layouts', 'Website')
				),
				'empty' => true
			],
			'og:image' => [
				'name' => __d('layouts', 'Open Graph Image'),
//				'type' => CmsLayoutAttributeType::TYPE_IMAGE
			]
		];

		$this->_contentAreas = [
			'main' => __d('layouts', 'Main'),
			'sidebar' => __d('layouts', 'Sidebar'),
			'third' => __d('layouts', 'Third Content Area')
		];
	}

}

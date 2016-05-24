<?php

namespace Wasabi\Theme\Basic\View\Layout;

use Wasabi\Cms\View\Layout\ContentArea;
use Wasabi\Cms\View\Layout\Layout;

class DefaultLayout extends Layout {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->_name = __d('wasabi_theme_default', 'Default');

		$this->_attributes = [
			'body_css_class' => [
				'name' => __d('wasabi_theme_default', 'Body CSS Class'),
//				'type' => CmsLayoutAttributeType::TYPE_TEXT
			],
			'og:title' => [
				'name' => __d('wasabi_theme_default', 'Open Graph Title'),
//				'type' => CmsLayoutAttributeType::TYPE_TEXT
			],
			'og:description' => [
				'name' => __d('wasabi_theme_default', 'Open Graph Description'),
//				'type' => CmsLayoutAttributeType::TYPE_TEXTAREA
			],
			'og:type' => [
				'name' => __d('wasabi_theme_default', 'Open Graph Type'),
//				'type' => CmsLayoutAttributeType::TYPE_SELECT,
				'options' => array(
					'article' => __d('wasabi_theme_default', 'Article'),
					'website' => __d('wasabi_theme_default', 'Website')
				),
				'empty' => true
			],
			'og:image' => [
				'name' => __d('wasabi_theme_default', 'Open Graph Image'),
//				'type' => CmsLayoutAttributeType::TYPE_IMAGE
			]
		];

		$this->_contentAreas = [
            new ContentArea('Top', 16, 16),
            new ContentArea('Main', 16, 16),
            new ContentArea('Bottom', 16, 16)
		];
	}

}

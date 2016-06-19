<?php

namespace WasabiTheme\Basic\View\Layout;

use Wasabi\Cms\View\Layout\AttributeType;
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
				'type' => AttributeType::TYPE_TEXT
			],
			'og:title' => [
				'name' => __d('wasabi_theme_default', 'Open Graph Title'),
				'type' => AttributeType::TYPE_TEXT
			],
			'og:description' => [
				'name' => __d('wasabi_theme_default', 'Open Graph Description'),
				'type' => AttributeType::TYPE_TEXTAREA
			],
			'og:type' => [
				'name' => __d('wasabi_theme_default', 'Open Graph Type'),
				'type' => AttributeType::TYPE_SELECT,
				'options' => array(
					'article' => __d('wasabi_theme_default', 'Article'),
					'website' => __d('wasabi_theme_default', 'Website')
				),
				'empty' => true
			],
			'og:image' => [
				'name' => __d('wasabi_theme_default', 'Open Graph Image'),
				'type' => AttributeType::TYPE_IMAGE
			]
		];

		$this->_contentAreas = [
            new ContentArea('Top', 16, 16),
            new ContentArea('Main', 16, 16),
            new ContentArea('Bottom', 16, 16)
		];
	}

}

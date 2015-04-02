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
 * @subpackage    Wasabi.Plugin.Blog.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('ClassRegistry', 'Utility');
App::uses('Router', 'Routing');
App::uses('WasabiNav', 'Core.Lib');
App::uses('Collections', 'Core.Lib');
App::uses('CollectionItems', 'Core.Lib');

class BlogEvents {

	public $implements = array(
		'Plugin.Collections.register' => array(
			'method' => 'registerCollections',
			'priority' => 0
		),
		'Plugin.CollectionItems.register' => array(
			'method' => 'registerCollectionItems',
			'priority' => 0
		)
	);

	public static function registerCollections(WasabiEvent $event) {
		Collections::instance()->register('blog_posts', array(
			'plugin' => 'Blog',
			'model' => 'BlogPost',
			'displayName' => __d('blog', 'Blog Posts')
		));
	}

	public static function registerCollectionItems(WasabiEvent $event) {
		CollectionItems::instance()->register('blog_post', array(
			'plugin' => 'Blog',
			'model' => 'BlogPost',
			'displayName' => __d('blog', 'Blog Post')
		));
	}

}

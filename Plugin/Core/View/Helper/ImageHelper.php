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
 * @subpackage    Wasabi.Plugin.Core.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');
App::uses('Folder', 'Utility');

/**
 * @property HtmlHelper $Html
 */
class ImageHelper extends AppHelper {

	/**
	 * Other Helpers used by this Helper.
	 *
	 * @var array
	 */
	public $helpers = array('Html');

	/**
	 * Resize/crop an image by given params
	 *
	 * @param array $media
	 * @param array $params
	 * @param array $options
	 * @return string
	 * @throws CakeException
	 */
	public function resize($media, $params = array(), $options = array()) {
		if (empty($media)) {
			user_error(__d('core', '$media is empty.'));
			return '';
		}

		$defaults = array(
			'resize_method' => 'resize',
			'width' => false,
			'height' => false,
			'cropFrom' => 'c', // crop the image from the specified position (c, tl, t, tr, r, br, b, bl, l)
			'tag' => true, // output img tag (true) or url only (false)
			'absolute' => false, // output absolute path to resized image (true)
			'fullUrl' => false, // output full url with http://.... (true) or relative url (false),
			'quality' => 90,
			'fillColor' => array(255, 255, 255)
		);
		$params = array_merge($defaults, $params);

		$img = $this->_loadImage($media, $params);

		if (!method_exists($img, $params['resize_method'])) {
			user_error(__d('core', 'Image resize method "%s" does not exist.', array($params['resize_method'])));
			return '';
		}

		if (!$img->isCached()) {
			try {
				call_user_func_array(array($img, $params['resize_method']), array($params));
			} catch (RuntimeException $e) {
				throw new CakeException($e->getMessage());
			}
		}

		if ($img->isCached()) {
			if ($params['absolute'] === true) {
				return $img->getTargetPath();
			}
			if ($params['tag'] === false) {
				$full = ($params['fullUrl'] === true);
				return Router::url($img->getTargetUri(), $full);
			}
			if (!isset($options['width']) && !isset($options['height'])) {
				$options['width'] = $img->getTargetWidth();
				$options['height'] = $img->getTargetHeight();
			}
			if ($params['fullUrl'] === true) {
				$options['fullBase'] = true;
			}
			$options['pathPrefix'] = $img->getRelTargetPath();
			return $this->Html->image($img->getTargetName(), $options);
		} else {
			user_error(__d('core', 'Resized image could not be created.'));
		}

		return '';
	}

	protected function _loadImage($media, $params) {
		try {
			$img = new Image($media, $params);
		} catch (CakeException $e) {
			user_error($e->getMessage());
			return '';
		}

		return $img;
	}
}

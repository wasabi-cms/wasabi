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

class MetaHelper extends AppHelper {

	public function meta($attributes) {
		$meta = array()
			+ $this->_general($attributes)
			+ $this->_openGraph($attributes)
			+ $this->_twitter($attributes)
			+ $this->_geo($attributes);

		$out = '';
		foreach ($meta as $property => $content) {
			$out .= '<meta name="' . $property . '" content="' . $content . '">';
		}

		return $out;
	}

	protected function _general($attributes) {
		$allowed = array('keywords', 'description', 'robots', 'viewport');

		return $this->_processAttributes($allowed, $attributes);
	}

	protected function _openGraph($attributes) {
		$allowed = array('og:sitename', 'og:title', 'og:description', 'og:image', 'og:image:type', 'og:image:width', 'og:image:height', 'og:url', 'og:locale', 'og:type','og:updated_time');

		return $this->_processAttributes($allowed, $attributes);
	}

	protected function _twitter($attributes) {
		$allowed = array('twitter:card', 'twitter:image', 'twitter:site', 'twitter:creator');

		return $this->_processAttributes($allowed, $attributes);
	}

	protected function _geo($attributes) {
		$allowed = array('icbm', 'geo.placename', 'geo.region');

		return $this->_processAttributes($allowed, $attributes);
	}

	protected function _processAttributes($allowed, $attributes) {
		$result = array();

		foreach($allowed as $property) {
			if (isset($attributes[$property]) && $attributes[$property] !== '') {
				$result[$property] = $attributes[$property];
			}
		}

		return $result;
	}

}
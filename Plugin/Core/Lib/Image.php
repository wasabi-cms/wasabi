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
 * @subpackage    Wasabi.Plugin.Core.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Image {

	/**
	 * Image instance
	 *
	 * @var \Imagine\Gd\Image|\Imagine\Imagick\Image
	 */
	protected $_image = null;

	/**
	 * Imagine instance for GD or ImageMagick.
	 *
	 * @var \Imagine\Gd\Imagine|\Imagine\Imagick\Imagine
	 */
	protected $_imagine = null;

	/**
	 * Media array from DB for the image that is to be cropped/resized.
	 *
	 * @var array
	 */
	protected $_media;

	/**
	 * Supported image mime types that can be cropped/resized.
	 *
	 * @var array
	 */
	protected $_supportedMimeTypes = array(
		'image/gif',
		'image/jpeg',
		'image/png'
	);

	/**
	 * Resize parameters.
	 *
	 * @var array
	 */
	protected $_params;

	/**
	 * Source image width.
	 *
	 * @var integer
	 */
	protected $_width;

	/**
	 * Source image height.
	 *
	 * @var integer
	 */
	protected $_height;

	/**
	 * Source image width/height ratio.
	 *
	 * @var float
	 */
	protected $_ratio;

	/**
	 * Target image width.
	 *
	 * @var integer
	 */
	protected $_targetWidth = null;

	/**
	 * Target image height.
	 *
	 * @var integer
	 */
	protected $_targetHeight = null;

	/**
	 * Determines if a cropped image has to be
	 * filled to match the target dimensions.
	 *
	 * @var boolean
	 */
	protected $_fillRequired = false;

	/**
	 * Width of the precrop resized image.
	 *
	 * @var integer
	 */
	protected $_tmpWidth;

	/**
	 * Height of the precrop resized image.
	 *
	 * @var integer
	 */
	protected $_tmpHeight;

	/**
	 * Upload dir of the source/cached image.
	 *
	 * @var string
	 */
	protected $_sourceDir;

	/**
	 * Upload path of the source image.
	 *
	 * @var string
	 */
	protected $_sourcePath;

	/**
	 * Name of the cropped/resized image
	 * including crop and size suffixes.
	 *
	 * @var string
	 */
	protected $_targetName;

	/**
	 * Target path of the resulting image.
	 *
	 * @var string
	 */
	protected $_targetPath;

	/**
	 * Target path for target directory relative to webroot with forward slashes.
	 *
	 * @var string
	 */
	protected $_targetRelPath;

	/**
	 * Target path for resized image relative to webroot with forward slashes.
	 *
	 * @var string
	 */
	protected $_targetUri;

	/**
	 * Supported crop types.
	 *
	 * `c`  - crop from center
	 * `tl` - crop from top left corner
	 * `t`  - crop from top center
	 * `tr` - crop from top right corner
	 * `r`  - crop from right center
	 * `br` - crop from bottom right corner
	 * `b`  - crop from bottom center
	 * `bl` - crop from bottom left corner
	 * `l`  - crop from left center
	 *
	 * @var array
	 * @TODO: implement cropTypes
	 */
	protected $_cropTypes = array('c', 'tl', 't', 'tr', 'r', 'br', 'b', 'bl', 'l');

	/**
	 * Image Constructor
	 *
	 * @param array $media
	 * @param array $params
	 */
	public function __construct($media, $params) {
		$this->_media = $media;
		$this->_params = $params;
		$this->_initSource();
		$this->_setupTarget();
	}

	/**
	 * Check if an up-to-date cached image exists.
	 *
	 * @return boolean
	 */
	public function isCached() {
		return (file_exists($this->_targetPath) && filemtime($this->_targetPath) > filemtime($this->_sourcePath));
	}

	/**
	 * Crop an image
	 *
	 * @return void
	 */
	public function crop() {
		$this->_initImagine();
		$this->_image = $this->_imagine->open($this->_sourcePath);
		/** @var \Imagine\Gd\Image|\Imagine\Imagick\Image $tmp */
		$tmp = $this->_image->copy()
			->resize(new Imagine\Image\Box($this->_tmpWidth, $this->_tmpHeight));

		if ($this->_fillRequired) {
			// fill
			$targetBox = new Imagine\Image\Box($this->_targetWidth, $this->_targetHeight);
			$color = new Imagine\Image\Color($this->_params['fillColor']);
			$nx = round(abs($this->_targetWidth - $this->_tmpWidth) / 2);
			$ny = round(abs($this->_targetHeight - $this->_tmpHeight) / 2);
			$point = new Imagine\Image\Point($nx, $ny);
			$this->_imagine->create($targetBox, $color)
				->paste($tmp, $point)
				->save($this->_targetPath, array('quality' => $this->_params['quality']));
		} else {
			// crop
			$cropStartX = round(abs($this->_tmpWidth - $this->_targetWidth) / 2);
			$cropStartY = round(abs($this->_tmpHeight - $this->_targetHeight) / 2);
			$point = new Imagine\Image\Point($cropStartX, $cropStartY);
			$targetBox = new Imagine\Image\Box($this->_targetWidth, $this->_targetHeight);
			$tmp->crop($point, $targetBox)
				->save($this->_targetPath, array('quality' => $this->_params['quality']));
		}
		$this->_crushPng();
	}

	/**
	 * Resize an image.
	 *
	 * @return void
	 */
	public function resize() {
		$this->_initImagine();
		$this->_image = $this->_imagine->open($this->_sourcePath)
			->resize(new Imagine\Image\Box($this->_targetWidth, $this->_targetHeight))
			->save($this->_targetPath, array('quality' => $this->_params['quality']));
		$this->_crushPng();
	}

	/**
	 * Get the absolute target path.
	 *
	 * @return string
	 */
	public function getTargetPath() {
		return $this->_targetPath;
	}

	/**
	 * Get the target path relative to webroot.
	 *
	 * @return string
	 */
	public function getRelTargetPath() {
		return $this->_targetRelPath;
	}

	/**
	 * Get the target uri relative to webroot.
	 *
	 * @return string
	 */
	public function getTargetUri() {
		return $this->_targetUri;
	}

	/**
	 * Get the file name of the cropped/resized image.
	 *
	 * @return string
	 */
	public function getTargetName() {
		return $this->_targetName;
	}

	/**
	 * Get the target width.
	 *
	 * @return integer
	 */
	public function getTargetWidth() {
		return $this->_targetWidth;
	}

	/**
	 * Get the target height.
	 *
	 * @return integer
	 */
	public function getTargetHeight() {
		return $this->_targetHeight;
	}

	/**
	 * Optimize png image file size with pngcrush
	 * if pngcrush is configured and enabled.
	 *
	 * @return void
	 */
	protected function _crushPng() {
		if ($this->_media['mime_type'] === 'image/png') {
			$pngCrushEnabled = (Configure::read('Settings.Core.Media.PngCrush.enabled') === '1');
			$pngCrushPath = Configure::read('Settings.Core.Media.PngCrush.path');
			if ($pngCrushEnabled && is_executable($pngCrushPath)) {
				$command = $pngCrushPath . ' -q -e opt.png ' . $this->_targetPath;
				@passthru($command);
				$optFile = new File(str_replace('.png', 'opt.png', $this->_targetPath), false);
				if ($optFile->exists()) {
					$cachedFile = new File($this->_targetPath, false);
					$cachedFile->write($optFile->read(true));
					$optFile->delete();
				}
			}
		}
	}

	/**
	 * Initialize the source image.
	 *
	 * @return void
	 * @throws CakeException
	 */
	protected function _initSource() {
		if (!in_array($this->_media['mime_type'], $this->_supportedMimeTypes)) {
			throw new CakeException(__d('core', 'Mime type "%s" of image "%s" is not supported.', array($this->_media['mime_type'], $this->_media['upload_path'])));
		}

		$this->_sourceDir  = APP . WEBROOT_DIR . DS . preg_replace('/\\\/', DS, preg_replace('/\\//', DS, $this->_media['upload_dir']));
		$this->_sourcePath = APP . WEBROOT_DIR . DS . preg_replace('/\\\/', DS, preg_replace('/\\//', DS, $this->_media['upload_path']));

		if (!file_exists($this->_sourcePath)) {
			throw new CakeException(__d('core', 'Image file "%s" is missing.', array($this->_media['upload_path'])));
		}

		$size = getimagesize($this->_sourcePath);
		if ($size === false) {
			throw new CakeException(__d('core', 'Can not retrieve image size of "%s".', array($this->_media['upload_path'])));
		}

		if ($size[0] == 0) {
			throw new CakeException(__d('core', 'Image "%s" has a width of "0px".', array($this->_media['upload_path'])));
		}

		if ($size[1] == 0) {
			throw new CakeException(__d('core', 'Image "%s" has a height of "0px".', array($this->_media['upload_path'])));
		}

		$this->_width = $size[0];
		$this->_height = $size[1];
		$this->_ratio = $this->_width / $this->_height;
	}

	/**
	 * Initialize the Imagine interface for ImageMagick or GD.
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	protected function _initImagine() {
		if ($this->_imagine !== null) {
			return;
		}

		try {
			$this->_imagine = new Imagine\Imagick\Imagine();
			return;
		} catch (RuntimeException $e) {}

		try {
			$this->_imagine = new \Imagine\Gd\Imagine();
		} catch (RuntimeException $e) {
			throw new RuntimeException(__d('core', 'Please install ImageMagick or PHP GD library.'));
		}
	}

	/**
	 * Setup the target image.
	 *
	 * @return void
	 */
	protected function _setupTarget() {
		$this->_calculateTargetSize();

		$cropSuffix = '';
		if ($this->_params['resize_method'] === 'crop') {
			$cropSuffix = $this->_params['cropFrom'];
		}

		$this->_targetName = $this->_media['name'] . '-' . $this->_targetWidth . 'x' . $this->_targetHeight . $cropSuffix . '.' . $this->_media['ext'];
		$this->_targetPath = $this->_sourceDir . DS . $this->_targetName;

		$this->_targetRelPath = preg_replace('/\\\/', '/', $this->_media['upload_dir']) . '/';
		$this->_targetUri = $this->_targetRelPath . $this->_targetName;
	}

	/**
	 * Calculate the target image dimensions and precrop values depending
	 * on the desired resize method.
	 *
	 * @return void
	 */
	protected function _calculateTargetSize() {
		if ($this->_targetWidth !== null && $this->_targetHeight !== null) {
			return;
		}

		$targetWidth = $this->_params['width'];
		$targetHeight = $this->_params['height'];

		switch ($this->_params['resize_method']) {
			case 'resize':
				// width supplied, no height supplied
				if ($targetWidth && $targetHeight === false) {
					if ($targetWidth < $this->_width) {
						$targetHeight = round($targetWidth / $this->_ratio);
					} else {
						$targetWidth = $this->_width;
						$targetHeight = $this->_height;
					}
				// no width supplied, height supplied
				} elseif ($targetWidth === false && $targetHeight) {
					if ($targetHeight < $this->_height) {
						$targetWidth = round($targetHeight * $this->_ratio);
					} else {
						$targetWidth = $this->_width;
						$targetHeight = $this->_height;
					}
				// width supplied and height supplied
				} else {
					$targetWidth = min($targetWidth, $this->_width);
					$targetHeight = min($targetHeight, $this->_height);
					$targetRatio = $targetWidth / $targetHeight;

					if ($targetRatio > $this->_ratio) {
						$targetWidth = round($targetHeight * $this->_ratio);
					} elseif ($targetRatio < $this->_ratio) {
						$targetHeight = round($targetWidth / $this->_ratio);
					}
				}
				break;

			case 'crop':
				if ( ($targetWidth > $this->_width) || ($targetHeight > $this->_height) ) {
					// fill the src image
					$targetRatio = max(
						$this->_width  / $targetWidth,
						$this->_height / $targetHeight
					);
					$this->_fillRequired = true;
					$this->_tmpWidth  = min($this->_width,  round($this->_width  / $targetRatio));
					$this->_tmpHeight = min($this->_height, round($this->_height / $targetRatio));
				} else {
					// crop the src image
					$targetRatio = min(
						$this->_width  / $targetWidth,
						$this->_height / $targetHeight
					);
					$this->_tmpWidth = round($this->_width / $targetRatio);
					$this->_tmpHeight = round($this->_height / $targetRatio);
				}
				break;
		}

		$this->_targetWidth = $targetWidth;
		$this->_targetHeight = $targetHeight;
	}

}

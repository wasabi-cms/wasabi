<?php

class Mime {

	protected static $_instance = null;

	protected $_mimeTypes;

	protected $_groups;

	protected $_groupsToMime;

	protected $_groupsToExt;

	protected static $_definition = array(
		// IMAGE
		'image/gif' => array('image', array('gif')),
		'image/png' => array('image', array('png', 'png')),
		'image/x-png' => array('image', array('png')),
		'image/jpeg' => array('image', array('jpg', 'jpeg', 'jpe')),
		'image/tiff' => array('image', array('tif', 'tiff')),
		'image/x-icon' => array('image', array('ico')),
		'image/x-ico' => array('image', array('ico')),
		'image/x-bmp' => array('image', array('bmp')),
		'image/x-ms-bmp' => array('image', array('bmp')),
		'image/bmp' => array('image', array('bmp')),
		'application/x-bmp' => array('image', array('bmp')),
		'application/bmp' => array('image', array('bmp')),
		'image/x-photoshop' => array('image', array('psd')),
		'image/psd' => array('image', array('psd')),
		'image/x-psd' => array('image', array('psd')),
		'image/photoshop' => array('image', array('psd')),
		'image/vnd.adobe.photoshop' => array('image', array('psd')),
		'image/svg' => array('image', array('svg')),
		'image/svg+xml' => array('image', array('svg', 'svgz')),
		'application/svg+xml' => array('image', array('svg')),
		'application/svg' => array('image', array('svg')),
		'image/ief' => array('image', array('ief')),
		'image/webp' => array('image', array('webp')),

		// AUDIO
		'audio/mpeg' => array('audio', array('mp3', 'mp2', 'mpga')),
		'audio/mp3' => array('audio', array('mp3')),
		'audio/mpeg3' => array('audio', array('mp3')),
		'audio/wav' => array('audio', array('wav')),
		'audio/x-wav' => array('audio', array('wav')),
		'audio/midi' => array('audio', array('mid', 'midi', 'kar')),
		'audio/basic' => array('audio', array('au', 'snd')),
		'audio/ogg' => array('audio', array('ogg', 'oga', 'spx')),
		'audio/x-ogg' => array('audio', array('oga', 'ogg', 'spx')),
		'audio/x-aiff' => array('audio', array('aiff', 'aif', 'aifc')),
		'audio/x-pn-realaudio' => array('audio', array('rm', 'ram')),
		'audio/x-realaudio' => array('audio', array('ra')),
		'audio/x-pn-realaudio-plugin' => array('audio', array('rpm')),
		'audio/webm' => array('audio', array('webm')),
		'audio/x-matroska' => array('audio', array('mka')),
		'audio/x-mpegurl' => array('audio', array('m3u')),
		'audio/aac' => array('audio', array('aac')),
		'audio/mp4' => array('audio', array('m4a', 'f4a', 'f4b')),
		'audio/x-ms-wma' => array('audio', array('wma')),

		// VIDEO
		'video/mpeg' => array('video', array('mpg', 'mpeg', 'mpe')),
		'video/ogg' => array('video', array('ogv')),
		'video/x-ogg' => array('video', array('ogv')),
		'video/x-flv' => array('video', array('flv')),
		'video/webm' => array('video', array('webm')),
		'video/x-matroska' => array('video', array('mkv', 'mka')),
		'video/quicktime' => array('video', array('mov', 'qt')),
		'video/x-msvideo' => array('video', array('avi')),
		'video/vnd.mpegurl' => array('video', array('mxu')),
		'video/x-sgi-movie' => array('video', array('movie')),
		'video/mp4' => array('video', array('mp4', 'm4v', 'f4v', 'f4p')),
		'video/x-ms-asf' => array('video', array('asf', 'asx')),
		'video/x-ms-wmv' => array('video', array('wmv')),

		// DOCUMENT
		'application/pdf' => array('document', array('pdf')),
		'application/msword' => array('document', array('doc', 'dot')),
		'application/vnd.ms-access' => array('document', array('mdb')),
		'application/vnd.ms-excel' => array('document', array('xls', 'xlt', 'xla', 'xll', 'xlm', 'xlw')),
		'application/vnd.ms-powerpoint' => array('document', array('ppt', 'pot', 'pps', 'ppa', 'ppz')),
		'text/rtf' => array('document', array('rtf')),
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => array('document', array('docx')),
		'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => array('document', array('dotx')),
		'application/vnd.ms-word.document.macroEnabled.12' => array('document', array('docm')),
		'application/vnd.ms-word.template.macroEnabled.12' => array('document', array('dotm')),
		'application/vnd.openxmlformats-officedocument.presentationml.template' => array('document', array('potx')),
		'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => array('document', array('ppsx')),
		'application/vnd.openxmlformats-officedocument.presentationml.presentation' => array('document', array('pptx')),
		'application/vnd.ms-powerpoint.addin.macroEnabled.12' => array('document', array('ppam')),
		'application/vnd.ms-powerpoint.presentation.macroEnabled.12' => array('document', array('pptm', 'potm')),
		'application/vnd.ms-powerpoint.slideshow.macroEnabled.12' => array('document', array('ppsm')),
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => array('document', array('xlsx')),
		'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => array('document', array('xltx')),
		'application/vnd.ms-excel.sheet.macroEnabled.12' => array('document', array('xlsm')),
		'application/vnd.ms-excel.template.macroEnabled.12' => array('document', array('xltm')),
		'application/vnd.ms-excel.addin.macroEnabled.12' => array('document', array('xlam')),
		'application/vnd.ms-excel.sheet.binary.macroEnabled.12' => array('document', array('xlsb')),
		'application/vnd.ms-xpsdocument' => array('document', array('xps')),

		// OTHER
		'application/postscript' => array('other', array('ai', 'eps', 'ps')),
		'application/x-latex' => array('other', array('latex')),
		'application/x-tex' => array('other', array('tex')),
		'application/x-texinfo' => array('other', array('texi', 'texinfo')),
		'application/x-dvi' => array('other', array('dvi')),
		'application/x-shockwave-flash' => array('other', array('swf')),
		'text/plain' => array('other', array('txt', 'csv', 'asc')),
		'text/html' => array('other', array('html', 'htm')),
		'application/json' => array('other', array('json')),
		'application/xhtml+xml' => array('other', array('xhtml', 'xht')),
		'application/xml' => array('other', array('xml', 'rdf')),
		'application/xslt+xml' => array('other', array('xslt')),
		'text/xml' => array('other', array('xml', 'xsl', 'xslt', 'rss', 'rdf')),
		'application/zip' => array('other', array('zip', 'jar', 'xpi', 'sxc', 'stc', 'sxd', 'std', 'sxi', 'sti', 'sxm', 'stm', 'sxw', 'stw')),
		'application/x-zip' => array('other', array('zip')),
		'application/x-zip-compressed' => array('other', array('zip')),
		'application/x-gzip' => array('other', array('gz')),
		'application/x-bzip' => array('other', array('bz2', 'gz')),
		'application/x-tar' => array('other', array('tar')),
		'application/x-gtar' => array('other', array('gtar', 'tar')),
		'application/x-jar' => array('other', array('jar')),
		'application/x-rar' => array('other', array('rar')),
		'application/x-7z-compressed' => array('other', array('7z')),
		'text/javascript' => array('other', array('js')),
		'application/x-javascript' => array('other', array('js')),
		'application/x-sh' => array('other', array('sh')),
		'application/octet-stream' => array('other', array('exe', 'bin', 'lha', 'lzh', 'safariextz')),
		'application/x-xpinstall' => array('other', array('xpi')),
		'text/calendar' => array('other', array('ics', 'ifb')),
		'text/css' => array('other', array('css')),
		'text/richtext' => array('other', array('rtx')),
		'application/rss+xml' => array('other', array('rss')),
		'text/csv' => array('other', array('csv')),
		'font/otf' => array('other', array('otf')),
		'font/ttf' => array('other', array('ttf', 'ttc')),
		'application/x-font-woff' => array('other', array('woff')),
		'text/tab-separated-values' => array('other', array('tsv')),
		'text/template' => array('other', array('tpl')),
		'application/atom+xml' => array('other', array('atom')),
		'application/x-chrome-extension' => array('other', array('crx')),
		'application/x-opera-extension' => array('other', array('oex')),
		'text/x-vcard' => array('other', array('vcf')),
		'application/x-msdownload' => array('other', array('exe'))
	);

	public function __construct() {
		$this->_mimeTypes = array_keys(self::$_definition);
		sort($this->_mimeTypes);

		$this->_groups = array();

		foreach (self::$_definition as $mime => $def) {
			if (!is_array($def) || count($def) !== 2) {
				continue;
			}

			list($group, $ext) = $def;

			if (!in_array($group, $this->_groups)) {
				$this->_groups[] = $group;
			}

			if (!isset($this->_groupsToMime[$group])) {
				$this->_groupsToMime[$group] = array();
			}
			$this->_groupsToMime[$group][] = $mime;

			if (!is_array($ext)) {
				continue;
			}
			if (!isset($this->_groupsToExt[$group])) {
				$this->_groupsToExt[$group] = array();
			}
			$this->_groupsToExt[$group] = array_merge($this->_groupsToExt[$group], $ext);
		}

		foreach ($this->_groups as $group) {
			if (isset($this->_groupsToMime[$group])) {
				$this->_groupsToMime[$group] = array_unique($this->_groupsToMime[$group]);
				sort($this->_groupsToMime[$group]);
			}
			if (isset($this->_groupsToExt[$group])) {
				$this->_groupsToExt[$group] = array_unique($this->_groupsToExt[$group]);
				sort($this->_groupsToExt[$group]);
			}
		}
	}

	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new Mime();
		}
		return self::$_instance;
	}

	public function getGroupForMime($mime) {
		if (isset(self::$_definition[$mime])) {
			return self::$_definition[$mime][0];
		}
		return 'other';
	}

	public static function addDefinitions($mimeTypes = array()) {
		if (empty($mime)) {
			return;
		}
		foreach ($mimeTypes as $mime => $def) {
			if (!is_string($mime) || !is_array($def) || count($def) !== 2) {
				continue;
			}
			if (isset(self::$_definition[$mime])) {
				list($group, $ext) = $def;
				if (is_string($group) && $group !== self::$_definition[$mime][0]) {
					self::$_definition[$mime][0] = $group;
				}
				if (!is_array($ext)) {
					continue;
				}
				self::$_definition[$mime][1] = array_merge(self::$_definition[$mime][1], $ext);
			}
		}
	}

	public function getTypes($group = false, $copyToKeys = false) {
		return $this->_getGeneric($this->_groupsToMime, $group, $copyToKeys);
	}

	public function getExtensions($group = false, $copyToKeys = false) {
		return $this->_getGeneric($this->_groupsToExt, $group, $copyToKeys);
	}

	protected function _getGeneric(&$what, $group = false, $copyToKeys = false) {
		if ($group === false) {
			if ($copyToKeys === false) {
				return $what;
			}
			$results = array();
			foreach ($what as $grp => $val) {
				$results[$grp] = array_combine($val, $val);
			}
			return $results;
		}
		if (!isset($what[$group])) {
			return array();
		}
		if ($copyToKeys === false) {
			return $what[$group];
		}
		return array_combine($what[$group], $what[$group]);
	}
}
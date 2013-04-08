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

class Textile {

	/**
	 * The view object using Textile
	 *
	 * @var View
	 */
	protected $_view;

	/**
	 * Holds all parts to be processed.
	 * After one part has been processed its content in this
	 * array will be updated.
	 *
	 * @var array
	 */
	protected $_parts = array();

	protected $_ignoreCodeBlocks = false;

	public $forceFullUrl = false;

	public $urlParams = false;

	/**
	 * Filters that are automatically called after textile conversion.
	 *
	 * @var array
	 */
	public static $filters = array(
		'text' => array(
			'self::filterBr'
		)
	);

	protected $_blockTags = array(
		'code'   => '<pre><code{{ATTR}}>{{CONTENT}}</code></pre>',
		'quote'  => '<blockquote{{ATTR}}>{{CONTENT}}</blockquote>',
		'div'    => '<div{{ATTR}}>{{CONTENT}}</div>',
		'span'   => '<span{{ATTR}}>{{CONTENT}}</span>',
		'table'  => '<table{{ATTR}}>{{CONTENT}}</table>',
		'not'    => '{{CONTENT}}',
		'pre'    => '<pre{{ATTR}}>{{CONTENT}}</pre>',
		'h[1-6]' => '<{{TAG}}{{ATTR}}>{{FORMAT}}{{CONTENT}}{{/FORMAT}}</{{TAG}}>',
		'p'      => '<p{{ATTR}}>{{FORMAT}}{{CONTENT}}{{/FORMAT}}</p>'
	);

	protected $_inlineTags = array(
		'@@' => array( '\@\@' , 'code'   ),
		'**' => array( '\*\*' , 'b'      ),
		'*'  => array( '\*'   , 'strong' ),
		'??' => array( '\?\?' , 'cite'   ),
		'-'  => array( '-'    , 'del'    ),
		'__' => array( '__'   , 'i'      ),
		'_'  => array( '_'    , 'em'     ),
		'%'  => array( '%'    , 'span'   ),
		'+'  => array( '\+'   , 'ins'    ),
		'~'  => array( '~'    , 'sub'    ),
		'^'  => array( '\^'   , 'sup'    )
	);

	protected $_urls = '[\w"$\-_.+!*\'(),";\/?:@=&%#{}|\\^~\[\]`]';

	/**
	 * Constructor
	 *
	 * @param View $view
	 */
	public function __construct(&$view) {
		$this->_view = $view;
	}

	/**
	 * Ignore code blocks while processing
	 *
	 * @param boolean $ignore
	 */
	public function ignoreCodeBlocks($ignore = true) {
		$this->_ignoreCodeBlocks = $ignore;
	}

	/**
	 * Convert textile input to html output
	 *
	 * @param string $text
	 * @return string
	 */
	public function convert($text) {
		$text = $this->_cleanUpLineEndings($text);
		$this->_parts = $this->_splitIntoParts($text);
		foreach ($this->_parts as &$part) {
			$disableFormat = false;
			$part = $this->_processPart($part, $disableFormat);
			if (!$disableFormat) {
				$part = $this->_formatText($part);
			}
		}
		$out = implode('', $this->_parts);
		$out = preg_replace("/<p><\/p>/", '', $out);
		return $out;
	}

	/**
	 * Process a part of textile
	 *
	 * @param $text
	 * @param $disableFormat
	 *
	 * @return string
	 */
	protected function _processPart($text, &$disableFormat) {
		$out = '';
		// match block tags
		preg_match("/^(" . implode('|', array_keys($this->_blockTags)) . ")(\(.*\))?\.\s(\n)?/", $text, $m);
		if (!empty($m)) {
			$tag = $m[1];
			$attributes = (isset($m[2])) ? ' ' . ltrim(rtrim($m[2], ')'), '(') : '';
			$content = mb_substr($text, mb_strlen($m[0]));
			$template = (isset($this->_blockTags[$tag])) ? $this->_blockTags[$tag] : $this->_blockTags['h[1-6]'];

			switch ($tag) {

				case 'div':
				case 'quote':
					// remove double space from each line
					$sublines = preg_split("/\\n/", $content);
					foreach ($sublines as &$line) {
						$line = preg_replace("/^\s{2}/", '', $line);
					}
					$content = implode("\n", $sublines);

					// split into lines by double \n and process each line
					// this allows for nested block elements within blockquotes and divs
					$sublines = preg_split("/\\n\\n/", $content);
					foreach ($sublines as &$line) {
						$line = $this->_processPart($line, $disableFormat);
					}
					$content = implode('', $sublines);
					break;

				case 'code':
					if ($this->_ignoreCodeBlocks) {
						return '';
					}
					$disableFormat = true;
					$sublines = preg_split("/\\n/", $content);
					foreach ($sublines as &$line) {
						$line = preg_replace("/^\s{2}/", '', $line);
					}
					$codeContent = implode("\n", $sublines);
					$attributes = explode('|', $attributes);

					if (count($attributes) > 1) {
						$lang = trim($attributes[0]);
						$info = trim($attributes[1]);
					} else {
						$lang = trim($attributes[0]);
						$info = strtoupper(trim($attributes[0]));
					}

					$out  = '<div class="code-title">' . $info . '</div>';
					$out .= '<div class="code">';
					$out .= '<table><tbody><tr>';

					$pygmentize = Configure::read('Wasabi.pygmentize_path');

					if ($pygmentize != 'full_path_to_pygmentize' && $pygmentize != '' && $pygmentize != false && $pygmentize != null) {
						// check the cache
						$cacheKey = md5($codeContent);

						$output = Cache::read($cacheKey, 'frontend.pygmentize');
						if (!$output) {
							$tmpName = tempnam('/tmp', 'pygmentize_');
							$fileHandle = fopen($tmpName, 'w');
							fwrite($fileHandle, $codeContent);
							fclose($fileHandle);

							$pygments = Configure::read('Wasabi.pygmentize_path');

							$command = '"' . $pygments . '" -l ' . $lang . ' -f html -O linenos=1,nowrap,encoding=utf-8,startinline "' . $tmpName . '"';
							$output = array();
							$retval = -1;

							exec($command, $output, $retval);
							unlink($tmpName);

							Cache::write($cacheKey, $output, 'frontend.pygmentize');
						}

						// line nos
						$out .= '<td class="linenos"><pre>';
						$out .= implode("\n", range(1, count($output)));
						$out .= '</pre></td>';

						// highlighted code
						$out .= '<td class="highlight ' . $lang . '"><pre>';
						$out .= implode("\n", $output);
						$out .= '</pre></td>';

					} else {

						// simple code (not pygmentized)
						$out .= '<td class="code ' . $lang . '"><pre>';
						$out .= $this->_encodeHtml($codeContent);
						$out .= '</pre></td>';

					}

					$out .= '</tr></tbody></table>';
					$out .= '</div>';

					return $out;

				case 'table':
					$sublines = preg_split("/\|$/m", $content);
					$theadTds = array();
					$tbodyTds = array();
					$content = '';
					foreach ($sublines as $key => $tr) {
						$tr = preg_replace("/^(\\n)?\s{2}\|/", '', $tr);
						if ($tr == '') {
							continue;
						}
						$tds = explode('|', $tr);
						foreach ($tds as $td) {
							if (preg_match("/^_\.\s(.*)?/", $td, $m)) {
								$theadTds[] = '{{FORMAT}}' . $m[1] . '{{/FORMAT}}';
							} else {
								$tbodyTds[$key][] = '{{FORMAT}}' . $td . '{{FORMAT}}';
							}
						}
					}
					if (!empty($theadTds)) {
						$content .= '<thead><tr><th>' . implode('</th><th>', $theadTds) . '</th></tr></thead>';
					}
					if (!empty($tbodyTds)) {
						$content .= '<tbody>';
						foreach ($tbodyTds as $row => $tds) {
							$content .= '<tr><td>' . implode('</td><td>', $tds) . '</td></tr>';
						}
						$content .= '</tbody>';
						$content = self::filterBr($this, $content, $this->_view);
					}
					break;

				case 'p':
					$content = self::_runFilters('text', $content);
					break;
			}

			$out = str_replace(
				array(
					'{{TAG}}',
					'{{ATTR}}',
					'{{CONTENT}}'
				),
				array(
					$tag,
					$attributes,
					$content
				),
				$template
			);
		// no block tags found
		} else {
			// Process Lists
			$sublines = preg_split("/\n(?=[*#])/m", $text);
			$isListItem = (boolean) preg_match("/^[*#]\s{1}/", $sublines[0]);
			if ($isListItem) {
				$ulListItems = array();
				$olListItems = array();

				foreach ($sublines as &$item) {
					preg_match("/^([*#]+)?\s(.*)?/", $item, $m);
					if (!empty($m)) {
						if ($m[1] == '*') {
							$ulListItems[] = array('content' => $m[2], 'children' => array());
						}
						if ($m[1] == '#') {
							$olListItems[] = array('content' => $m[2], 'children' => array());
						}
						if (mb_strlen($item) > mb_strlen($m[0])) {
							$subContent = mb_substr($item, mb_strlen($m[0]));
							$subContentLines = preg_split("/\\n/", $subContent);
							foreach ($subContentLines as $key => &$line) {
								$line = preg_replace("/^\s{2}/", '', $line);
								if ($line == '') {
									unset($subContentLines[$key]);
								}
							}
							if ($m[1] == '*') {
								$ulListItems[$this->_endKey($ulListItems)]['children'] = $subContentLines;
							}
							if ($m[1] == '#') {
								$olListItems[$this->_endKey($olListItems)]['children'] = $subContentLines;
							}
						}
					}
				}
				if (!empty($ulListItems)) {
					$out = '<ul>';
					foreach ($ulListItems as &$item) {
						$out .= '<li>{{FORMAT}}' . $item['content'] . '{{/FORMAT}}';
						if (!empty($item['children'])) {
							$item['children'] = implode("\n", $item['children']);
							$out .= $this->_processPart($item['children'], $disableFormat);
						}
						$out .= '</li>';
					}
					$out .= '</ul>';
				} elseif (!empty($olListItems)) {
					$out = '<ol>';
					foreach ($olListItems as &$item) {
						$out .= '<li>{{FORMAT}}' . $item['content'] . '{{/FORMAT}}';
						if (!empty($item['children'])) {
							$item['children'] = implode("\n", $item['children']);
							$out .= $this->_processPart($item['children'], $disableFormat);
						}
						$out .= '</li>';
					}
					$out .= '</ol>';
				}
			} else {
				$text = self::_runFilters('text', $text);
				$out = '<p>{{FORMAT}}' . $text . '{{/FORMAT}}</p>';
			}
		}
		return $out;
	}

	protected function _cleanUpLineEndings($text) {
		return str_replace("\r\n", "\n", $text);
	}

	/**
	 * Encode some html special chars in string $str.
	 *
	 * @param string $str
	 * @param boolean $quotes
	 * @return string
	 */
	protected function _encodeHtml($str, $quotes = true) {
		$a = array(
			'&' => '&amp;',
			'<' => '&lt;',
			'>' => '&gt;'
		);
		if ($quotes) {
			$a = $a + array(
				"'" => '&#39;',
				'"' => '&quot;'
			);
		}

		return strtr($str, $a);
	}

	/**
	 * Return the last key index of an array
	 *
	 * @param $array
	 * @return integer
	 */
	protected function _endKey($array) {
		end($array);
		return key($array);
	}

	protected function _formatText($text) {
		$formatSplits = preg_split('/((\{\{FORMAT\}\})|(\{\{\\/FORMAT\}\}))/', $text);
		foreach ($formatSplits as &$split) {
			$pnct = ".,\"'?!;:";

			foreach ($this->_inlineTags as $f) {
				$f = $f[0];
				$split = preg_replace_callback("/
					(^|(?<=[\s>$pnct\(])|[{[])
					($f)(?!$f)
					(\(.*\))?
					(?::(\S+))?
					([^\s$f]+|\S.*?[^\s$f\n])
					([$pnct]*)
					$f
					($|[\]}]|(?=[[:punct:]]{1,2}|\s|\)))
					/x", array(&$this, "_formatInlineTags"), $split
				);
			}

			$split = preg_replace_callback('/
				"([^"]+?)"                 # link text
				(\[.*\])?                  # title
				(\|\|.*\|\|)?              # atts
				([!])?                     # target blank?
				:
				(' . $this->_urls . '+?)   # url
				(\/)?                      # slashes in url
				([^\w\/;]*?)               # post vars in url
				([\]}]|(?=\s|$|\)))
				/x', array(&$this, "_formatLink"), $split
			);

		}

		return implode('', $formatSplits);
	}

	protected function _formatInlineTags($m) {
		$attributes = '';
		if ($m[3] != '') {
			$attributes .= ' ' . ltrim(rtrim($m[3], ')'), '(');
		}
		$out = '<' . $this->_inlineTags[$m[2]][1] . $attributes . '>';
		if ($m[2] == '@@') {
			$out .= $this->_encodeHtml($m[5]) . $this->_encodeHtml($m[6]);
		} else {
			$out .= $m[5];
		}
		$out .= '</' . $this->_inlineTags[$m[2]][1] . '>';
		return $out;
	}

	protected function _formatLink($m) {
		$linkText = $m[1];
		$title = '';
		if ($m[2] != '') {
			$title = ltrim(rtrim($m[2], ']'), '[');
		}
		$attributes = '';
		if ($m[3] != '') {
			$attributes = ' ' . ltrim(rtrim($m[3], '||'), '||');
		}
		$target = '';
		if ($m[4] == '!') {
			$target = ' target="_blank"';
		}
		$url = $this->_runFilters('link', $m[5]);
		$post = '';
		if ($m[7] != null) {
			$post = $m[7];
		}
		return '<a href="' . $url . '" title="' . $title . '"' . $attributes . $target . '>' . $linkText . '</a>' . $post;
	}

	protected function _splitIntoParts($text) {
		return preg_split("/\\n\\n/", $text);
	}

	protected function _runFilters($type, $text) {
		if (isset(self::$filters[$type])) {
			foreach (self::$filters[$type] as $filter) {
				$text = call_user_func($filter, $this, $text, $this->_view);
			}
		}
		return $text;
	}

	public static function addFilter($type, $function) {
		self::$filters[$type][] = $function;
	}

	public static function filterBr($tex, $text, $view) {
		return preg_replace('/\\n/', '<br>', $text);
	}
}

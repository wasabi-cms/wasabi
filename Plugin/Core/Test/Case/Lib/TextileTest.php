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
 * @subpackage    Wasabi.Plugin.Core.Test.Case.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Cache', 'Cache');
App::uses('CakeTestCase', 'TestSuite');
App::uses('Configure', 'Core');
App::uses('Controller', 'Controller');
App::uses('Textile', 'Core.Lib');
App::uses('View', 'View');

class TestTextile extends Textile {

	public function cleanUpLineEndings($text) {
		return $this->_cleanUpLineEndings($text);
	}

	public function getIgnoreCodeBlocks() {
		return $this->_ignoreCodeBlocks;
	}

	public function getView() {
		return $this->_view;
	}

	public function encodeHtml($str, $quotes = true) {
		return $this->_encodeHtml($str, $quotes);
	}

	public function endKey($array) {
		return $this->_endKey($array);
	}

	public function splitIntoParts($text) {
		return $this->_splitIntoParts($text);
	}

}

class TextileTest extends CakeTestCase {

	/**
	 * @var TestTextile
	 */
	public $Textile = null;

	public function setUp() {
		parent::setUp();

		$Controller = new Controller();
		$View = new View($Controller);
		$this->Textile = new TestTextile($View);
	}

	public function testAddFilder() {
		$current_filters = TestTextile::$filters;
		$this->assertEqual(count($current_filters['text']), 1);

		TestTextile::addFilter('text', 'TestClass::textFilter');
		$result = TestTextile::$filters;
		$this->assertEqual(count($result['text']), 2);

		TestTextile::$filters = $current_filters;

		TestTextile::addFilter('link', 'TestClass::linkFilter');
		$result = TestTextile::$filters;
		$this->assertEqual(count($result['text']), 1);
		$this->assertEqual(count($result['link']), 1);

		TestTextile::$filters = $current_filters;
	}

	public function testCleanUpLineEndings() {
		$text = "\r\n";
		$expected = "\n";
		$result = $this->Textile->cleanUpLineEndings($text);
		$this->assertEqual($expected, $result);

		$text = "\r\n\n";
		$expected = "\n\n";
		$result = $this->Textile->cleanUpLineEndings($text);
		$this->assertEqual($expected, $result);

		$text = "\n\n";
		$expected = "\n\n";
		$result = $this->Textile->cleanUpLineEndings($text);
		$this->assertEqual($expected, $result);
	}

	public function testConstructor() {
		$expected = 'View';
		$result = get_class($this->Textile->getView());
		$this->assertEqual($expected, $result);
	}

	public function testSimpleTextFormatting() {
		$str = "*Hello* _World_! **Hello** __World__!";
		$expected = "<p><strong>Hello</strong> <em>World</em>! <b>Hello</b> <i>World</i>!</p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		$str = "*_Hello_* -World-!";
		$expected = "<p><strong><em>Hello</em></strong> <del>World</del>!</p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		$str = "*Hello.*";
		$expected = "<p><strong>Hello.</strong></p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);
	}

	public function testPartSeparationAndLineBreaksInParts() {
		/**
		 *
		 *
		 *
		 *
		 * Hello World!
		 *
		 * Hello
		 * World 2!
		 *
		 * Blub
		 *
		 *
		 *
		 * Blub 2
		 *
		 *
		 *
		 *
		 */
		$str = "\n\n\n\nHello World!\n\nHello\nWorld 2!\n\nBlub\n\n\n\nBlub 2\n\n\n\n";
		$expected = "<p>Hello World!</p><p>Hello<br>World 2!</p><p>Blub</p><p>Blub 2</p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);
	}

	public function testBlockElements() {
		/**
		 * div.
		 *   Hello World!
		 *   Blub
		 *
		 *   Hello World 2!
		 */
		$str = "div.\n  Hello World!\n  Blub\n  \n  Hello World 2!";
		$expected = "<div><p>Hello World!<br>Blub</p><p>Hello World 2!</p></div>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * quote.
		 *   Hello World!
		 *   Blub
		 *
		 *   Hello World 2!
		 */
		$str = "quote.\n  Hello World!\n  Blub\n  \n  Hello World 2!";
		$expected = "<blockquote><p>Hello World!<br>Blub</p><p>Hello World 2!</p></blockquote>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);
	}

	public function testPartAttributes() {
		/**
		 * *(class="test")Hello*
		 */
		$str = "*(class=\"test\")Hello*";
		$expected = "<p><strong class=\"test\">Hello</strong></p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * p(class="test"). *_Hello_* -World-!
		 */
		$str = "p(class=\"test\"). *_Hello_* -World-!";
		$expected = "<p class=\"test\"><strong><em>Hello</em></strong> <del>World</del>!</p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * div(class="test ab" data-mode="test").
		 *   Hello World
		 */
		$str = "div(class=\"test ab\" data-mode=\"test\").\n  Hello World";
		$expected = "<div class=\"test ab\" data-mode=\"test\"><p>Hello World</p></div>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * quote(class="test ab" data-mode="test").
		 *   Hello World
		 */
		$str = "quote(class=\"test ab\" data-mode=\"test\").\n  Hello World";
		$expected = "<blockquote class=\"test ab\" data-mode=\"test\"><p>Hello World</p></blockquote>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);
	}

	public function testNestedParts() {
		/**
		 * div(class="one").
		 *   Hello World!
		 *
		 *   div(class="two").
		 *     Hello World 2!
		 */
		$str = "div(class=\"one\").\n  Hello World!\n  \n  div(class=\"two\").\n    Hello World 2!";
		$expected = "<div class=\"one\"><p>Hello World!</p><div class=\"two\"><p>Hello World 2!</p></div></div>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * quote(class="one").
		 *   Hello World!
		 *
		 *   div(class="two").
		 *     Hello World 2!
		 */
		$str = "quote(class=\"one\").\n  Hello World!\n  \n  div(class=\"two\").\n    Hello World 2!";
		$expected = "<blockquote class=\"one\"><p>Hello World!</p><div class=\"two\"><p>Hello World 2!</p></div></blockquote>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);
	}

	public function testLinks() {
		/**
		 * "link text":http://example.com
		 */
		$str = "\"link text\":http://example.com";
		$expected = "<p><a href=\"http://example.com\" title=\"\">link text</a></p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * "link text"[link title]:http://example.com
		 */
		$str = "\"link text\"[link title]:http://example.com";
		$expected = "<p><a href=\"http://example.com\" title=\"link title\">link text</a></p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * "link text"!:http://example.com
		 */
		$str = "\"link text\"!:http://example.com";
		$expected = "<p><a href=\"http://example.com\" title=\"\" target=\"_blank\">link text</a></p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * "link text"[link title]!:http://example.com
		 */
		$str = "\"link text\"[link title]!:http://example.com";
		$expected = "<p><a href=\"http://example.com\" title=\"link title\" target=\"_blank\">link text</a></p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * "link text"||class="test-link"||:http://example.com
		 */
		$str = "\"link text\"||class=\"test-link\"||:http://example.com";
		$expected = "<p><a href=\"http://example.com\" title=\"\" class=\"test-link\">link text</a></p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * "link text"[link title]||class="test-link"||!:http://example.com
		 */
		$str = "\"link text\"[link title]||class=\"test-link\"||!:http://example.com";
		$expected = "<p><a href=\"http://example.com\" title=\"link title\" class=\"test-link\" target=\"_blank\">link text</a></p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * "link text":http://example.com Hello World
		 */
		$str = "\"link text\":http://example.com Hello World";
		$expected = "<p><a href=\"http://example.com\" title=\"\">link text</a> Hello World</p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * Hello "World":http://example.com
		 */
		$str = "Hello \"World\":http://example.com.";
		$expected = "<p>Hello <a href=\"http://example.com\" title=\"\">World</a>.</p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);
	}

	public function testInlineCodeElements() {
		//
		// Hello World @@public $str = false@@!
		//
		$str = "Hello World @@public \$str = false@@!";
		$expected = "<p>Hello World <code>public \$str = false</code>!</p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		//
		// Hello World @@public $str = false;@@!
		//
		$str = "Hello World @@public \$str = false;@@!";
		$expected = "<p>Hello World <code>public \$str = false;</code>!</p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		//
		// Hello World @@public $str = 'test'@@!
		//
		$str = "Hello World @@public \$str = 'test'@@!";
		$expected = "<p>Hello World <code>public \$str = &#39;test&#39;</code>!</p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		//
		// Hello World @@public $str = 'test';@@!
		//
		$str = "Hello World @@public \$str = 'test';@@!";
		$expected = "<p>Hello World <code>public \$str = &#39;test&#39;;</code>!</p>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);
	}

	public function testBlockCode() {
		// code(lexer|optional title for code block).
		/**
		 * code(js|An examplar code block title).
		 *   $('html').trigger('test_event');
		 */
		$str = "code(js|An exemplar code block title).\n  $('html').trigger('test_event');";
		$expected = "<div class=\"code-title\">An exemplar code block title</div><div class=\"code\"><table><tbody><tr><td class=\"code js\"><pre>$(&#39;html&#39;).trigger(&#39;test_event&#39;);</pre></td></tr></tbody></table></div>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * code(js).
		 *   $('html').trigger('test_event');
		 */
		$str = "code(js).\n  $('html').trigger('test_event');";
		$expected = "<div class=\"code-title\">JS</div><div class=\"code\"><table><tbody><tr><td class=\"code js\"><pre>$(&#39;html&#39;).trigger(&#39;test_event&#39;);</pre></td></tr></tbody></table></div>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * test that the ignore code block setting works
		 * -> if ignore code blocks is set to true, code blocks should be skipped from processing
		 */
		$this->Textile->ignoreCodeBlocks();
		$expected = "";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);
		$this->Textile->ignoreCodeBlocks(false);

		/**
		 * test working pygments / pygmentize
		 * For testing on Windows systems make sure that pygmentize.exe is available in Path.
		 * For testing on Linux/Unix/OSX systems make sure that pygmentize is available in PATH.
		 *
		 * code(js).
		 *   $('html').trigger('test_event');
		 */
		Configure::write('Wasabi.pygmentize_path', 'pygmentize');
		if ($this->_isPygmentizeAvailable()) {
			$str = "code(js).\n  $('html').trigger('test_event');";
			$expected = "<div class=\"code-title\">JS</div><div class=\"code\"><table><tbody><tr><td class=\"linenos\"><pre>1</pre></td><td class=\"highlight js\"><pre><span class=\"nx\">$</span><span class=\"p\">(</span><span class=\"s1\">&#39;html&#39;</span><span class=\"p\">).</span><span class=\"nx\">trigger</span><span class=\"p\">(</span><span class=\"s1\">&#39;test_event&#39;</span><span class=\"p\">);</span></pre></td></tr></tbody></table></div>";
			$result = $this->Textile->convert($str);
			$this->assertEqual($expected, $result);

			// test pygmentize block caching
			$cache_key = md5("$('html').trigger('test_event');");
			$is_cached = (boolean) Cache::read($cache_key, 'frontend.pygmentize');
			$this->assertTrue($is_cached);

			// reset pygmentize config & delete cache
			Configure::write('Wasabi.pygmentize_path', 'full_path_to_pygmentize');
			Cache::delete($cache_key, 'frontend.pygmentize');
		}
	}

	public function testTables() {
		/**
		 * table.
		 *   |_. head one|_. head two|_. head three|
		 *   |text one|text two|text three|
		 */
		$str = "table.\n  |_. head one|_. head two|_. head three|\n  |text one|text two| text three|";
		$expected = "<table><thead><tr><th>head one</th><th>head two</th><th>head three</th></tr></thead><tbody><tr><td>text one</td><td>text two</td><td> text three</td></tr></tbody></table>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * table(class="foo" data-test-attribute="bar").
		 *   |_. head one|_. head two|_. head three|
		 *   |text one|text two|text three|
		 */
		$str = "table(class=\"foo\" data-test-attribute=\"bar\").\n  |_. head one|_. head two|_. head three|\n  |text one|text two| text three|";
		$expected = "<table class=\"foo\" data-test-attribute=\"bar\"><thead><tr><th>head one</th><th>head two</th><th>head three</th></tr></thead><tbody><tr><td>text one</td><td>text two</td><td> text three</td></tr></tbody></table>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);
	}

	public function testLists() {
		/**
		 * Unordered List
		 *
		 * * list item one
		 * * list item two
		 * * list item three
		 */
		$str = "* list item one\n* list item two\n* list item three";
		$expected = "<ul><li>list item one</li><li>list item two</li><li>list item three</li></ul>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * Ordered List
		 *
		 * # list item one
		 * # list item two
		 * # list item three
		 */
		$str = "# list item one\n# list item two\n# list item three";
		$expected = "<ol><li>list item one</li><li>list item two</li><li>list item three</li></ol>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * Nested List
		 *
		 * * list item one
		 * * list item two
		 * * list item three
		 *
		 *   # nested list item one
		 *   # nested list item two
		 */
		$str = "* list item one\n* list item two\n* list item three\n  \n  # nested list item one\n  # nested list item two";
		$expected = "<ul><li>list item one</li><li>list item two</li><li>list item three<ol><li>nested list item one</li><li>nested list item two</li></ol></li></ul>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * Nested List
		 *
		 * # list item one
		 * # list item two
		 * # list item three
		 *
		 *   * nested list item one
		 *   * nested list item two
		 */
		$str = "# list item one\n# list item two\n# list item three\n  \n  * nested list item one\n  * nested list item two";
		$expected = "<ol><li>list item one</li><li>list item two</li><li>list item three<ul><li>nested list item one</li><li>nested list item two</li></ul></li></ol>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);

		/**
		 * Multiple Nested Blocks in List
		 *
		 * * list item one
		 * * list item two
		 * * list item three
		 *
		 *   div(class="test").
		 *     nested div element
		 *     linebreak test
		 *
		 *     * super deep
		 *     * super deep two
		 */
		$str = "* list item one\n* list item two\n* list item three\n  \n  div(class=\"test\").\n    nested div element\n    linebreak test\n    \n    * super deep\n    * super deep two";
		$expected = "<ul><li>list item one</li><li>list item two</li><li>list item three<div class=\"test\"><p>nested div element<br>linebreak test</p><ul><li>super deep</li><li>super deep two</li></ul></div></li></ul>";
		$result = $this->Textile->convert($str);
		$this->assertEqual($expected, $result);
	}

	public function testEncodeHtml() {
		/**
		 * Hi "Tarzan" & 'Jane'. 3 > 2 < 3
		 */
		$str = 'Hi "Tarzan" & \'Jane\'. 3 > 2 < 3';

		$expected = 'Hi &quot;Tarzan&quot; &amp; &#39;Jane&#39;. 3 &gt; 2 &lt; 3';
		$result = $this->Textile->encodeHtml($str);
		$this->assertEqual($expected, $result);

		$expected = 'Hi "Tarzan" &amp; \'Jane\'. 3 &gt; 2 &lt; 3';
		$result = $this->Textile->encodeHtml($str, false);
		$this->assertEqual($expected, $result);
	}

	public function testEndKey() {
		$a = array('Zero');
		$expected = 0;
		$result = $this->Textile->endKey($a);
		$this->assertEqual($expected, $result);

		$a = array('Zero', 'One');
		$expected = 1;
		$result = $this->Textile->endKey($a);
		$this->assertEqual($expected, $result);

		$a = array('Zero', 'One', 'Two');
		$expected = 2;
		$result = $this->Textile->endKey($a);
		$this->assertEqual($expected, $result);
	}

	public function testIgnoreCodeBlocks() {
		$this->Textile->ignoreCodeBlocks();
		$this->assertTrue($this->Textile->getIgnoreCodeBlocks());

		$this->Textile->ignoreCodeBlocks(false);
		$this->assertFalse($this->Textile->getIgnoreCodeBlocks());

		$this->Textile->ignoreCodeBlocks(true);
		$this->assertTrue($this->Textile->getIgnoreCodeBlocks());
	}

	public function testSplitIntoParts() {
		/**
		 * Hello
		 */
		$text = "Hello";
		$expected = array(
			'Hello'
		);
		$result = $this->Textile->splitIntoParts($text);
		$this->assertEqual($expected, $result);

		/**
		 * Hello
		 *
		 * World
		 */
		$text = "Hello\n\nWorld";
		$expected = array(
			'Hello',
			'World'
		);
		$result = $this->Textile->splitIntoParts($text);
		$this->assertEqual($expected, $result);

		/**
		 * Hello
		 *
		 * World
		 *
		 * !
		 */
		$text = "Hello\n\nWorld\n\n!";
		$expected = array(
			'Hello',
			'World',
			'!'
		);
		$result = $this->Textile->splitIntoParts($text);
		$this->assertEqual($expected, $result);
	}

	public function tearDown() {
		$this->Textile = null;
	}

	private function _isPygmentizeAvailable() {
		$pygmentize_path = Configure::read('Wasabi.pygmentize_path');
		if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
			$fp = popen("where $pygmentize_path", "r");
			$result = fgets($fp, 255);
			$exists = !preg_match('#Could not find files#', $result);
			pclose($fp);
		} else {  # non-Windows
			$fp = popen("which $pygmentize_path", "r");
			$result = fgets($fp, 255);
			$exists = !empty($result);
			pclose($fp);
		}
		return $exists;
	}
}

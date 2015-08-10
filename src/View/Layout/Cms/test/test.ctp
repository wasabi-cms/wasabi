<?php
/**
 * @var CmsPageView $this
 * @var string		$title_for_layout
 * @var string		$lang
 * @var array		$layoutAttributes
 */

$this->extend('../default/default');

$this->append('bottom_body');
$this->end('bottom_body');

echo $this->fetch('content');

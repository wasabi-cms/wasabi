<?php
/**
 * @var CmsView $this
 */

$this->extend('Core.default');

$this->append('head_css');
echo $this->WasabiAsset->css('/css/live-edit.css', 'Cms');
$this->end();

$this->append('afterContent');
echo $this->element('toolbox');
$this->end();

$this->append('bottom_js');
echo $this->WasabiAsset->js('/js/live-edit.js', 'Cms');
$this->end();

echo $this->fetch('content');

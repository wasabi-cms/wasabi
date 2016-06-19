<?php
/**
 * @var \WasabiTheme\Basic\View\BasicThemeView $this
 * @var \Wasabi\Cms\Model\Entity\Page $page
 * @var string $title_for_layout
 * @var string $lang
 * @var array $layoutAttributes
 */
use Cake\Routing\Router;
use Wasabi\Cms\WasabiCms;
use Wasabi\Core\Wasabi;

$this->start('head_css');
echo $this->Asset->css('css/main.css', 'WasabiTheme/Basic');
$this->end();

$this->start('head_meta');
$this->end();

?><!doctype html>
<html class="no-js" lang="<?= Wasabi::contentLanguage()->lang ?>">
<head>
    <?= $this->Meta->title($page) ?>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <?= $this->Meta->description($page->meta_description) ?>
    <?= $this->Meta->viewport('width=device-width, initial-scale=1, user-scalable=yes') ?>
    <?= $this->Meta->applicationName(WasabiCms::$instanceName) ?>
    <?= $this->Meta->robots($page) ?>
    <?= $this->Meta->displaySearchBox() ?>
    <?= $this->Meta->googleSiteVerification() ?>
    <?= $this->Meta->twitter($page) ?>
    <?= $this->Meta->opengraph($page) ?>
    <?= $this->fetch('head_meta') ?>
    <?= $this->Meta->article($page) ?>
    <?= $this->Meta->dcDateIssued($page) ?>
    <?= $this->fetch('head_css') ?>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo Router::url('/favicon.ico') ?>">
	<link  rel="apple-touch-icon" href="<?php echo Router::url('/apple-touch-icon.png') ?>">
    <?= $this->Meta->ldJson() ?>
</head>
<body class="<?= $this->bodyCssClass() ?>">

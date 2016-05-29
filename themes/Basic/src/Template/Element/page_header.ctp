<?php
/**
 * @var \WasabiTheme\Basic\View\BasicThemeView $this
 * @var string		$title_for_layout
 * @var string		$lang
 * @var array		$layoutAttributes
 */
use Cake\Routing\Router;use Wasabi\Core\Wasabi;

?><!doctype html>
<html class="no-js" lang="<?= Wasabi::contentLanguage()->lang ?>">
<head>
	<meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->get('title') ?></title>
    <meta name="application-name" content="Application Name">
    <meta name="description" content="150 chars">
    <meta name="subject" content="your website's subject">
    <meta name="robots" content="index,follow,noodp">
    <meta name="googlebot" content="index,follow">
    <meta name="google" content="nositelinkssearchbox">
    <meta name="google-site-verification" content="verification_token">
	<?php
	#echo $this->Meta->meta($layoutAttributes);

	$this->start('head_css');
	echo $this->Asset->css('css/main.css', 'WasabiTheme/Basic');
	echo $this->Asset->js('ASSETS/js/test.js');
	$this->end();
	echo $this->fetch('head_css');
	?>
	<link href="<?php echo Router::url('/favicon.ico') ?>" type="image/x-icon" rel="shortcut icon">
	<link href="<?php echo Router::url('/apple-touch-icon.png') ?>" rel="apple-touch-icon">
</head>
<body class="<?= $this->bodyCssClass() ?>">

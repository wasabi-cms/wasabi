<?php
/**
 * @var CmsPageView $this
 * @var string		$title_for_layout
 * @var string		$lang
 * @var array		$layoutAttributes
 */
?><!doctype html>
<!--[if lt IE 7]>     <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="<?php echo $lang ?>"><![endif]-->
<!--[if IE 7]>        <html class="no-js lt-ie9 lt-ie8" lang="<?php echo $lang ?>"><![endif]-->
<!--[if IE 8]>        <html class="no-js lt-ie9" lang="<?php echo $lang ?>"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="<?php echo $lang ?>"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta content="text/html;" http-equiv="content-type">
	<title><?php echo $title_for_layout ?></title>
	<?php
	echo $this->Meta->meta($layoutAttributes);

	$this->start('head_css');
	echo $this->WasabiAsset->css('/css/all.css');
	$this->end('head_css');
	echo $this->fetch('head_css');
	?>
	<link href="<?php echo Router::url('/favicon.ico') ?>" type="image/x-icon" rel="shortcut icon">
	<link href="<?php echo Router::url('/apple-touch-icon.png') ?>" rel="apple-touch-icon">
	<!--[if lt IE 9]><?php echo $this->WasabiAsset->js('/js/html5shiv.js') ?><![endif]-->
</head>
<body<?php echo (isset($layoutAttributes['body_css_class'])) ? ' class="' . $layoutAttributes['body_css_class'] . '"' : '' ?>>
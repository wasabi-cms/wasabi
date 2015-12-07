<?php
/**
 * @var \Wasabi\ThemeDefault\View\ThemeDefaultView $this
 * @var string		$title_for_layout
 * @var string		$lang
 * @var array		$layoutAttributes
 */
use Cake\Routing\Router;use Wasabi\Core\Wasabi;

?><!doctype html>
<html class="no-js" lang="<?= Wasabi::contentLanguage()->lang ?>">
<head>
	<meta charset="utf-8">
	<meta content="text/html;" http-equiv="content-type">
	<title><?= $this->get('title') ?></title>
	<?php
	#echo $this->Meta->meta($layoutAttributes);

	$this->start('head_css');
	echo $this->Asset->css('/css/all');
	$this->end();
	echo $this->fetch('head_css');
	?>
	<link href="<?php echo Router::url('/favicon.ico') ?>" type="image/x-icon" rel="shortcut icon">
	<link href="<?php echo Router::url('/apple-touch-icon.png') ?>" rel="apple-touch-icon">
</head>
<body<?php #echo (isset($layoutAttributes['body_css_class'])) ? ' class="' . $layoutAttributes['body_css_class'] . '"' : '' ?>>
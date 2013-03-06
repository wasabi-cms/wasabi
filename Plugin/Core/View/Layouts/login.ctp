<!doctype html>
<!--[if lt IE 7]>     <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en-US"><![endif]-->
<!--[if IE 7]>        <html class="no-js lt-ie9 lt-ie8" lang="en-US"><![endif]-->
<!--[if IE 8]>        <html class="no-js lt-ie9" lang="en-US"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en-US"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo $title_for_layout; ?></title>
	<meta name="viewport" content="width=device-width">
	<?php
	echo $this->fetch('meta');
	echo $this->WasabiAsset->css('/css/app.css', 'Core');
	if (Configure::read('debug') > 0) {
		echo $this->WasabiAsset->css('/css/debug.css', 'Core');
	}
	echo $this->Html->meta('icon');
	?>
</head>
<body class="login">
	<div class="login-wrapper">
		<div class="round-shadow">
			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<?php echo $this->element('Core.footer'); ?>
</body>
</html>
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
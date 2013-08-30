<?php
/**
 * @var CoreView $this
 * @var $title_for_layout
 */
?>
<meta charset="utf-8">
<title><?php echo $title_for_layout . ' - ' . Configure::read('Settings.core.application_name') ?></title>
<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700">
<?php
echo $this->fetch('meta');
echo $this->WasabiAsset->css('/css/styles.css', 'Core');

$this->start('head_css');
$this->end('head_css');
echo $this->fetch('head_css');

if (Configure::read('debug') > 0) {
	echo $this->WasabiAsset->css('/css/debug.css', 'Core');
}
echo $this->Html->meta('icon');
?>
<!--[if lt IE 9]><?php echo $this->WasabiAsset->js('/js/html5shiv.js', 'Core') ?><![endif]-->
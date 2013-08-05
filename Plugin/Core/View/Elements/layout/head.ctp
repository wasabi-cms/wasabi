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
if ($this->request->params['plugin'] !== 'core' && file_exists(CakePlugin::path(Inflector::camelize($this->request->params['plugin'])) . 'webroot' . DS . 'css' . DS . 'app.css')) {
	echo $this->WasabiAsset->css('/css/app.css', Inflector::camelize($this->request->params['plugin']));
}
if (Configure::read('debug') > 0) {
	echo $this->WasabiAsset->css('/css/debug.css', 'Core');
}
echo $this->Html->meta('icon');
?>
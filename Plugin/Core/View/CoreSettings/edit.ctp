<?php
/**
 * @var CoreView $this
 * @var array $cacheDurations
 */

$this->CHtml->setTitle(__d('core', 'Edit Core Settings'));

echo $this->Form->create('CoreSetting');
echo $this->CForm->input('application_name', array('label' => __d('core', 'Application Name'), 'label_info' => __d('core', 'The application name is used to identify your app')));
echo $this->CForm->input('enable_caching', array('label' => __d('cms', 'Enable Caching?'), 'label_info' => __d('core', 'This is used as a default setting and can be overriden by individual plugins.'), 'options' => array(0 => __d('core', 'No'), 1 => __d('core', 'Yes')), 'empty' => false));
echo $this->CForm->input('cache_duration', array('label' => __d('core', 'Cache Duration'), 'label_info' => __d('core', 'This is used as a default setting and can be overriden by individual plugins.'), 'options' => $cacheDurations));
?>
<div class="form-controls">
	<?php
	echo $this->Form->button('<span>' . __d('core', 'Save') . '</span>', array('div' => false, 'class' => 'button'));
	echo $this->CHtml->backendLink(__d('core', 'Reset'), '/settings/edit');
	?>
</div>
<?php echo $this->Form->end(); ?>
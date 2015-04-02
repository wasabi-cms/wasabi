<?php
/**
 * @var CoreView $this
 * @var array $cacheDurations
 */

$this->Html->setTitle(__d('core', 'Edit Cache Settings'));

echo $this->Form->create('CoreCacheSetting', array('class' => 'no-top-section'));
echo $this->CForm->input('enable_caching', array('label' => __d('cms', 'Enable Caching?'), 'label_info' => __d('core', 'This is a global setting and cannot be overriden by individual plugins.'), 'options' => array(0 => __d('core', 'No'), 1 => __d('core', 'Yes')), 'empty' => false));
echo $this->CForm->input('cache_duration', array('label' => __d('core', 'Cache Duration'), 'label_info' => __d('core', 'This is used as a default setting and can be overriden by individual plugins.'), 'options' => $cacheDurations));
?>
<div class="form-controls">
	<?php
	echo $this->Form->button(__d('core', 'Save'), array('div' => false, 'class' => 'button'));
	echo $this->Html->backendLink(__d('core', 'Reset'), '/settings/cache');
	?>
</div>
<?php echo $this->Form->end(); ?>
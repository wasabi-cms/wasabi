<?php
/**
 * @var CoreView $this
 */

$this->CHtml->setTitle(__d('core', 'Edit Core Settings'));

echo $this->Form->create('CoreSetting');
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->CForm->input('application_name', array('label' => __d('core', 'Application Name'), 'label_info' => __d('core', 'The application name is used to identify your app')));
echo $this->CForm->input('enable_caching', array('label' => __d('cms', 'Enable Caching?'), 'label_info' => __d('core', 'This is used as a default setting and can be overriden by individual plugins.'), 'options' => array(0 => __d('core', 'No'), 1 => __d('core', 'Yes')), 'empty' => false));
echo $this->CForm->input('cache_time', array('label' => __d('core', 'Cache Duration'), 'label_info' => __d('core', 'This is used as a default setting and can be overriden by individual plugins.'), 'options' => array(
	'1 hour' => __d('core', '1 hour'),
	'2 hours' => __d('core', '%s hours', array(2)),
	'4 hours' => __d('core', '%s hours', array(4)),
	'8 hours' => __d('core', '%s hours', array(8)),
	'16 hours' => __d('core', '%s hours', array(16)),
	'1 day' => __d('core', '1 day'),
	'2 days' => __d('core', '%s days', array(2)),
	'5 days' => __d('core', '%s days', array(5)),
	'7 days' => __d('core', '%s days', array(7)),
	'14 days' => __d('core', '%s days', array(14)),
	'30 days' => __d('core', '%s days', array(30)),
	'60 days' => __d('core', '%s days', array(60)),
	'90 days' => __d('core', '%s days', array(90)),
	'180 days' => __d('core', '%s days', array(180)),
	'365 days' => __d('core', '%s days', array(365)),
	'999 days' => __d('core', '%s days', array(999)),
)));
?>
<div class="form-controls">
	<?php
	echo $this->Form->button('<span>' . __d('core', 'Save') . '</span>', array('div' => false, 'class' => 'button'));
	echo $this->CHtml->backendLink(__d('core', 'Reset'), '/settings/edit');
	?>
</div>
<?php echo $this->Form->end(); ?>
<?php
/**
 * @var CoreView $this
 * @var array $cacheDurations
 */

$this->CHtml->setTitle(__d('core', 'Edit Media Settings'));

echo $this->Form->create('CoreMediaSetting');
//echo $this->CForm->input('application_name', array('label' => __d('core', 'Application Name'), 'label_info' => __d('core', 'The application name is used to identify your app')));
//echo $this->CForm->input('Login__Message__show', array('label' => __d('core', 'Login Message'), 'label_info' => __d('core', 'Enable or disable an optional message that is displayed on top of the login page.'), 'type' => 'checkbox'));
//echo $this->CForm->input('Login__Message__text', array('label' => __d('core', 'Login Message Text'), 'label_info' => __d('core', 'The text of the login message.'), 'info' => __d('core', 'allowed Html tags: &lt;b&gt;&lt;strong&gt;&lt;a&gt;&lt;br&gt;&lt;br/&gt;'), 'type' => 'textarea', 'rows' => 2));
//echo $this->CForm->input('Login__Message__class', array('label' => __d('core', 'Login Message Class'), 'label_info' => __d('core', 'The CSS class that is applied to the message box.'), 'options' => array('info' => 'info', 'warning' => 'warning', 'error' => 'error')));
?>
<div class="form-controls">
	<?php
	echo $this->Form->button('<span>' . __d('core', 'Save') . '</span>', array('div' => false, 'class' => 'button'));
	echo $this->CHtml->backendLink(__d('core', 'Reset'), '/settings/media');
	?>
</div>
<?php echo $this->Form->end(); ?>
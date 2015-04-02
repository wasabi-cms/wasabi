<?php
/**
 * @var CoreView $this
 */

$this->Html->setTitle(__d('core', 'Edit General Settings'));

echo $this->Form->create('CoreGeneralSetting');
echo $this->CForm->section(
	__d('core', 'Application'),
	__d('core', 'General application settings.')
);
echo $this->CForm->input('application_name', array(
	'label' => __d('core', 'Application Name'),
	'label_info' => __d('core', 'The application name is used to identify your app')
));
echo $this->CForm->section(
	__d('core', 'Login Message'),
	__d('core', 'An optional message that is displayed on top of the login page.')
);
echo $this->CForm->input('Login__Message__show', array(
	'label' => __d('core', 'Display Login Message?'),
	'options' => array(
		'0' => __d('core', 'No'),
		'1' => __d('core', 'Yes')
	)
));
echo $this->CForm->input('Login__Message__text', array(
	'label' => __d('core', 'Login Message Text'),
	'label_info' => __d('core', 'The text of the login message.'),
	'info' => __d('core', 'allowed Html tags: &lt;b&gt;&lt;strong&gt;&lt;a&gt;&lt;br&gt;&lt;br/&gt;'),
	'type' => 'textarea',
	'rows' => 2
));
echo $this->CForm->input('Login__Message__class', array(
	'label' => __d('core', 'Login Message Class'),
	'label_info' => __d('core', 'The CSS class that is applied to the message box.'),
	'options' => array(
		'info' => 'info',
		'warning' => 'warning',
		'error' => 'error'
	)
));
?>
<div class="form-controls">
	<?php
	echo $this->Form->button(__d('core', 'Save'), array('div' => false, 'class' => 'button'));
	echo $this->Html->backendLink(__d('core', 'Reset'), '/settings/general');
	?>
</div>
<?php echo $this->Form->end(); ?>
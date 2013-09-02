<?php
/**
 * @var CoreView $this
 * @var array $subDirectories
 */

$this->CHtml->setTitle(__d('core', 'Edit Media Settings'));

echo $this->Form->create('CoreMediaSetting');
echo $this->CForm->section(
	__d('core', 'Uploads'),
	__d('core', 'General upload settings.')
);
echo $this->CForm->input('Media__upload_directory', array(
	'label' => __d('core', 'Upload Directory'),
	'label_info' => __d('core', 'Directory where all your media files are uploaded to.'),
	'info' => __d('core', 'starting on app/webroot/ (e.g: uploads)')
));
echo $this->CForm->input('Media__upload_subdirectories', array(
	'label' => __d('core', 'Create Subdirectories?'),
	'label_info' => __d('core', 'Organize your uploaded files into subdirecties.'),
	'info' => __d('core', 'Using a subdirectory template avoids duplicate file names in the long run<br/>and helps your users to differentiate between up-to-date and older files.'),
	'options' => $subDirectories
));
echo $this->CForm->section(
	__d('core', 'PngCrush'),
	__d('core', 'PngCrush is an image optimizer that can drastically reduce the filesize of png images.')
);
echo $this->CForm->input('Media__PngCrush__enabled', array(
	'label' => __d('core', 'Enable PngCrush?'),
	'options' => array('0' => __d('core', 'No'), '1' => __d('core', 'Yes'))
));
echo $this->CForm->input('Media__PngCrush__path', array(
	'label' => __d('core', 'PngCrush path'),
	'label_info' => __d('core', 'The <strong>absolute</strong> path to your PngCrush executable.')
));
?>
<div class="form-controls">
	<?php
	echo $this->Form->button('<span>' . __d('core', 'Save') . '</span>', array('div' => false, 'class' => 'button'));
	echo $this->CHtml->backendLink(__d('core', 'Reset'), '/settings/media');
	?>
</div>
<?php echo $this->Form->end(); ?>
<?php
/**
 * @var CoreView $this
 * @var array $subDirectories
 * @var array $mimeTypes
 * @var array $fileExtensions
 */

$this->start('Core.requireJs');
echo 'wasabi.core.settingsMedia();';
$this->end();

$this->Html->setTitle(__d('core', 'Edit Media Settings'));

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
?>
<div class="form-row row">
	<label for="CoreMediaSettingMediaAllowedMimeTypes">
		<?php echo __d('core', 'Mime Types') ?>
		<small><?php echo __d('core', 'Select all Mime Types that should be allowed to upload.') ?></small>
	</label>
	<div class="field top-pd">
		<div class="sub-row">
			<?php
			echo $this->Form->input('Media__allow_all_mime_types', array(
				'label' => __d('core', 'Allow all Mime Types'),
				'type' => 'checkbox'
			));
			?>
		</div>
		<?php
		$options = array(
			'label' => false,
			'options' => $mimeTypes,
			'multiple' => true,
			'class' => 'mselect'
		);
		if (isset($this->data['CoreMediaSetting']) &&
			isset($this->data['CoreMediaSetting']['Media__allow_all_mime_types']) &&
			$this->data['CoreMediaSetting']['Media__allow_all_mime_types'] === '1'
		) {
			$options['disabled'] = true;
		}
		echo $this->Form->input('Media__allowed_mime_types', $options);
		?>
	</div>
</div>
	<div class="form-row row">
		<label for="CoreMediaSettingMediaAllowedFileExtensions">
			<?php echo __d('core', 'File Extensions') ?>
			<small><?php echo __d('core', 'Select all File Extensions that should be allowed to upload.') ?></small>
		</label>
		<div class="field top-pd">
			<div class="sub-row">
				<?php
				echo $this->Form->input('Media__allow_all_file_extensions', array(
					'label' => __d('core', 'Allow all File Extensions'),
					'type' => 'checkbox'
				));
				?>
			</div>
			<?php
			$options = array(
				'label' => false,
				'options' => $fileExtensions,
				'multiple' => true,
				'class' => 'mselect'
			);
			if (isset($this->data['CoreMediaSetting']) &&
				isset($this->data['CoreMediaSetting']['Media__allow_all_file_extensions']) &&
				$this->data['CoreMediaSetting']['Media__allow_all_file_extensions'] === '1'
			) {
				$options['disabled'] = true;
			}
			echo $this->Form->input('Media__allowed_file_extensions', $options);
			?>
		</div>
	</div>
<?php
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
	echo $this->Form->button(__d('core', 'Save'), array('div' => false, 'class' => 'button'));
	echo $this->Html->backendLink(__d('core', 'Reset'), '/settings/media');
	?>
</div>
<?php echo $this->Form->end(); ?>
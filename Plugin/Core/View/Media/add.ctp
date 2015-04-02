<?php
/**
 * @var CoreView $this
 * @var array $media
 */

$this->Html->setTitle(__d('core', 'Add Media'));

$allExtensions = Configure::read('Settings.Core.Media.allow_all_file_extensions');
$allowedExtensions = Configure::read('Settings.Core.Media.allowed_file_extensions');
$ext = '';
if ($allExtensions !== null || is_array($allowedExtensions)) {
	$ext .= ' data-extensions="';
	if ($allExtensions === '1') {
		$ext .= '*';
	} else {
		$ext .= join('|', $allowedExtensions);
	}
	$ext .= '"';
}

$allMimeTypes = Configure::read('Settings.Core.Media.allow_all_mime_types');
$allowedMimeTypes = Configure::read('Settings.Core.Media.allowed_mime_types');
$mime = '';
if ($allMimeTypes !== null || is_array($allowedMimeTypes)) {
	$mime .= ' data-mime-types="';
	if ($allMimeTypes === '1') {
		$mime .= '*';
	} else {
		$mime .= join('|', $allowedMimeTypes);
	}
	$mime .= '"';
}
?>
<div class="multi-fileupload" data-url="<?php echo Router::url(array('plugin' => 'core', 'controller' => 'media', 'action' => 'upload')) ?>"<?php echo $ext . $mime ?>>
	<div class="multi-fileupload-box row">
		<div class="multi-fileupload-button">
			<span class="button fileinput-button">
				<i class="icon-plus"></i>
				<span><?php echo __d('core', 'Add Files') ?>...</span>
				<input type="file" name="data[files][]" multiple>
			</span>
		</div>
		<p class="multi-fileupload-description"><?php echo __d('core', 'Drag and drop a single or multiple files on this area to upload them.') ?></p>
	</div>
	<ul class="multi-fileupload-controls">
		<li><span class="button small start-upload disabled" data-action="upload-all"><i class="icon-upload-alt"></i><span><?php echo __d('core', 'Start Upload') ?></span></span></li>
		<li><span class="button small cancel-upload disabled" data-action="cancel-all"><i class="icon-ban-circle"></i><span><?php echo __d('core', 'Cancel') ?></span></span></li>
	</ul>
	<div class="multi-fileupload-progress inactive">
		<div class="progress">
			<div class="progress-bar" style="width: 0%;"><span class="progress-text">0%</span></div>
		</div>
		<div class="progress-extended invisible"><?php echo __d('core', 'Waiting for upload...') ?></div>
	</div>
	<table class="multi-fileupload-upload">
		<tbody></tbody>
	</table>
</div>
<?php $this->append('bottom_js'); ?>
<script type="text/javascript">
	$(function () {
		'use strict';

		$('.multi-fileupload')
			.multifileupload({
				url: $('.multi-fileupload').first().attr('data-url'),
				extensions: ['jpg', 'jpeg', 'gif', 'bmp', 'png', 'pdf', 'psd', 'rar', 'zip', 'mp3', 'mp4', '3gp', 'avi', 'xls', 'xlsx'],
				uploadAllBtn: '[data-action="upload-all"]',
				cancelAllBtn: '[data-action="cancel-all"]',
				forceIframeTransport: false,
				previewImageWidth: 60,
				previewImageHeight: 60,
				maxFileSize: 104857600
			})
			//-------------------- local events ---------------------------------------------------------------
			.on('add.mfu', function(event, fs) {
				var $uploadHolder = fs.mfu.$el.find('.multi-fileupload-upload').find('tbody');
				var $filename = $('<td/>').addClass('t5 filename');

				$.each(fs.files, function(index, file) {
					if (index > 0) {
						$filename.append('<br/>');
					}

					$filename.append(file.name);

					if (!file.validates && file.errors.length > 0) {
						$filename.append('<br/>').append(
							$('<span/>').addClass('error-message').html(file.errors.join('<br/>'))
						);
					}
				});

				fs.$context = $('<tr/>');

				if (!fs.validates) {
					fs.$context.addClass('error');

					if (fs.errors.length > 0) {
						$filename.append('<br/>').append(
							$('<span/>').addClass('error-message').html(fs.errors.join('<br/>'))
						)
					}
				}

				fs.$uploadBtn = $('<span/>')
					.addClass('button small')
					.append($('<i class="icon-upload-alt"/>'))
					.append($('<span/>').text(wasabi.i18n('Start')));

				fs.$cancelBtn = $('<span/>')
					.addClass('button small')
					.append($('<i class="icon-ban-circle"/>'))
					.append($('<span/>').text(wasabi.i18n('Cancel')));

				fs.$removeBtn = $('<span/>')
					.addClass('button small')
					.append($('<i class="icon-remove"/>'))
					.append($('<span/>').text(wasabi.i18n('Remove')));

				fs.$context
					.append($('<td/>').addClass('t3 preview'))
					.append($filename)
					.append(
						$('<td/>')
							.addClass('t4 progress-single')
							.append(
								$('<span/>')
									.addClass('size')
									.html(fs.mfu.bytesToHuman(fs.size, 2))
							)
							.append(
								$('<div/>')
									.addClass('progress-strip')
									.append($('<div/>').addClass('progress-bar'))
							)
					)
					.append(
						$('<td/>')
							.addClass('t4 actions')
							.append(fs.$uploadBtn)
							.append(fs.$cancelBtn)
							.append(fs.$removeBtn)
					);

				fs.$context.appendTo($uploadHolder);
			})
			.on('processed.mfu', function(event, file) {
				if (file.$canvas !== undefined) {
					file.fileSet.$context.find('.preview').append(file.$canvas);
				} else if (file.$audio !== undefined) {
					file.fileSet.$context.find('.preview').append(file.$audio);
				} else if (file.$video !== undefined) {
					file.fileSet.$context.find('.preview').append(file.$video);
				} else {
					file.fileSet.$context.find('.preview').append(
						$('<div/>')
							.css({
								width: file.fileSet.mfu.options.previewImageWidth,
								height: file.fileSet.mfu.options.previewImageHeight,
								backgroundColor: '#ccc',
								lineHeight: file.fileSet.mfu.options.previewImageHeight + 'px',
								textAlign: 'center'
							})
							.html(file.name.split('.').pop())
					);
				}
			})
			.on('remove-local.mfu', function(event, fs) {
				fs.$context.find('td').wrapInner('<div style="overflow: hidden;"/>').parent().find('td > div').slideUp(200, function() {
					fs.$context.remove();
				});
			})
			.on('progress-local.mfu', function(event, percent, fs) {
				fs.$context.find('.progress-bar').first().css({
					width: percent + '%'
				});
			})
			.on('cancel-local.mfu', function(event, fs) {
				fs.$context.find('.progress-bar').first().css({
					width: 0 + 'px'
				});
			})
			.on('complete-local.mfu', function(event, fs) {
				fs.$context.addClass('success').find('.progress-bar').first().css({
					width: 100 + '%'
				});
				setTimeout(function() {
					fs.$context.find('td').wrapInner('<div/>').parent().find('td > div').slideUp(200, function() {
						fs.$context.remove();
					});
				}, 2000);
			})
			//-------------------- global events ---------------------------------------------------------------
			.on('start.mfu', function(event, mfu) {
				mfu.$el.find('.multi-fileupload-progress')
					.removeClass('inactive')
					.find('.progress-extended')
					.removeClass('invisible');
			})
			.on('progress.mfu', function(event, progress, mfu) {
				var text = '';

				mfu.$el.find('.multi-fileupload-progress')
					.find('.progress-bar').first()
					.css({
						width: progress.percent + '%'
					})
					.find('.progress-text').text(progress.percent + '%');

				text += wasabi.i18n('Uploaded {0} / {1} Files', [
					progress.filesLoaded,
					progress.filesTotal
				]) + ' | ';

				if (mfu.bitRateTimer !== undefined) {
					text += mfu.bitRateToHuman(progress.bitrate, 2) + ' | ';
				}

				if (progress.total !== undefined) {
					text += mfu.bytesToHuman(progress.loaded, 2) + ' / ' +
						mfu.bytesToHuman(progress.total, 2) + ' | ';
				}

				text += progress.percent + '%';

				mfu.$el.find('.multi-fileupload-progress')
					.find('.progress-extended').text(text);
			})
			.on('error.mfu', function(event, errors, fs) {
				fs.$context.addClass('error');
				if (errors && errors.length > 0) {
					var $filename = fs.$context.find('.filename');
					$.each(errors, function(index, error) {
						$filename
							.append('<br/>')
							.append(
								$('<span/>').addClass('error-message').html(error)
							);
					});
				}
			});
	});
</script>
<?php $this->end(); ?>

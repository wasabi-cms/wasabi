<?php
/**
 * @var CoreView $this
 */

$this->CHtml->setTitle(__d('core', 'Media'));

?>
<?php $this->append('head_css'); ?>
<style type="text/css">
	.fileinput-button {
		overflow: hidden;
		position: relative;
		display: inline-block;
	}
	.fileinput-button input {
		cursor: pointer;
		direction: ltr;
		font-size: 23px;
		margin: 0;
		opacity: 0;
		position: absolute;
		right: 0;
		top: 0;
		transform: translate(-300px, 0px) scale(4);
	}
	.fileupload-buttonbar .btn, .fileupload-buttonbar .toggle {
		margin-bottom: 5px;
	}
	.progress-animated .progress-bar, .progress-animated .bar {
		background: url("../img/progressbar.gif") repeat scroll 0 0 transparent !important;
		filter: none;
	}
	.fileupload-loading {
		background: url("../img/loading.gif") no-repeat scroll center center / contain transparent;
		display: none;
		float: right;
		height: 32px;
		width: 32px;
	}
	.fileupload-processing .fileupload-loading {
		display: block;
	}
	.files audio, .files video {
		max-width: 300px;
	}
	.fileupload-buttonbar .toggle, .files .toggle, .files .btn span {
		display: none;
	}
	.files .name {
		width: 80px;
		word-wrap: break-word;
	}
	.files audio, .files video {
		max-width: 80px;
	}
</style>
<?php $this->end(); ?>
<!-- The file upload form used as target for the file upload widget -->
<form id="fileupload" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
	<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
	<div class="row fileupload-buttonbar">
		<div class="span7">
			<!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
			<button type="submit" class="btn btn-primary start">
				<i class="icon-upload icon-white"></i>
				<span>Start upload</span>
			</button>
			<!-- Show this button only if an upload is in progress. -->
			<button type="reset" class="btn btn-warning cancel">
				<i class="icon-ban-circle icon-white"></i>
				<span>Cancel upload</span>
			</button>
			<!-- The loading indicator is shown during file processing -->
			<span class="fileupload-loading"></span>
		</div>
		<!-- The global progress information -->
		<div class="span5 fileupload-progress fade">
			<!-- The global progress bar -->
			<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
				<div class="bar" style="width:0%;"></div>
			</div>
			<!-- The extended global progress information -->
			<div class="progress-extended">&nbsp;</div>
		</div>
	</div>
	<!-- The table listing the files available for upload/download -->
	<table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
</form>
<?php $this->append('bottom_js'); ?>
<script type="text/javascript">
	$(function () {
		'use strict';
		// Change this to the location of your server-side upload handler:
		var url = window.location.hostname === 'blueimp.github.io' ?
				'//jquery-file-upload.appspot.com/' : 'server/php/',
			uploadButton = $('<button/>')
				.addClass('btn')
				.prop('disabled', true)
				.text('Processing...')
				.on('click', function () {
					var $this = $(this),
						data = $this.data();
					$this
						.off('click')
						.text('Abort')
						.on('click', function () {
							$this.remove();
							data.abort();
						});
					data.submit().always(function () {
						$this.remove();
					});
				});
		$('#fileupload').fileupload({
			url: url,
			dataType: 'json',
			autoUpload: false,
			acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
			maxFileSize: 5000000, // 5 MB
			// Enable image resizing, except for Android and Opera,
			// which actually support image resizing, but fail to
			// send Blob objects via XHR requests:
			disableImageResize: /Android(?!.*Chrome)|Opera/
				.test(window.navigator.userAgent),
			previewMaxWidth: 100,
			previewMaxHeight: 100,
			previewCrop: true
		}).on('fileuploadadd', function (e, data) {
				data.context = $('<div/>').appendTo('#files');
				$.each(data.files, function (index, file) {
					var node = $('<p/>')
						.append($('<span/>').text(file.name));
					if (!index) {
						node
							.append('<br>')
							.append(uploadButton.clone(true).data(data));
					}
					node.appendTo(data.context);
				});
			}).on('fileuploadprocessalways', function (e, data) {
				var index = data.index,
					file = data.files[index],
					node = $(data.context.children()[index]);
				if (file.preview) {
					node
						.prepend('<br>')
						.prepend(file.preview);
				}
				if (file.error) {
					node
						.append('<br>')
						.append(file.error);
				}
				if (index + 1 === data.files.length) {
					data.context.find('button')
						.text('Upload')
						.prop('disabled', !!data.files.error);
				}
			}).on('fileuploadprogressall', function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('#progress .bar').css(
					'width',
					progress + '%'
				);
			}).on('fileuploaddone', function (e, data) {
				$.each(data.result.files, function (index, file) {
					var link = $('<a>')
						.attr('target', '_blank')
						.prop('href', file.url);
					$(data.context.children()[index])
						.wrap(link);
				});
			}).on('fileuploadfail', function (e, data) {
				$.each(data.result.files, function (index, file) {
					var error = $('<span/>').text(file.error);
					$(data.context.children()[index])
						.append('<br>')
						.append(error);
				});
			}).prop('disabled', !$.support.fileInput)
			.parent().addClass($.support.fileInput ? undefined : 'disabled');
	});
</script>
<?php $this->end(); ?>

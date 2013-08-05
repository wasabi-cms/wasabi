<div id="<?php echo $id ?>" class="hide">
	<div class="modal-header">
		<h2><?php echo $title ?></h2>
	</div>
	<div class="modal-body">
		<?php echo $body ?>
	</div>
	<div class="modal-footer modal-confirm">
		<form action="<?php echo $action ?>" method="<?php echo $method ?>"<?php echo ($ajax === true) ? ' data-is-ajax="true"' : ''; echo ($notify !== false) ? ' data-notify="' . $notify . '"' : ''; ?>>
			<button type="submit" class="button"><span><?php echo __d('core', 'Yes'); ?></span></button>
			<a href="javascript:void(0)" data-dismiss="modal"><?php echo __d('core', 'No') ?></a>
		</form>
	</div>
</div>
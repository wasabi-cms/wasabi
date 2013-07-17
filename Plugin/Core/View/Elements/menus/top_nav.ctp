<?php
/**
 * @var CoreView $this
 */
?>
<?php if (isset($backend_menu_for_layout) && isset($backend_menu_for_layout['primary'])): ?>
	<ul class="main-nav">
		<?php echo $this->Navigation->render($backend_menu_for_layout['primary']); ?>
	</ul>
<?php endif; ?>
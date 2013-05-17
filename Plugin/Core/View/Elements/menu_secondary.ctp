<?php
/**
 * @var CoreView $this
 * @var array $backend_menu_for_layout
 */
?>
<?php if (isset($backend_menu_for_layout) && isset($backend_menu_for_layout['secondary']) && !empty($backend_menu_for_layout['secondary'])): ?>
<ul class="sub-menu">
	<?php echo $this->Navigation->render($backend_menu_for_layout['secondary']); ?>
</ul>
<?php endif; ?>
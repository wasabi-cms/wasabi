<?php /** @var CoreView $this */ ?>
<?php if (isset($backend_menu_for_layout) && isset($backend_menu_for_layout['secondary'])): ?>
<ul class="sub-menu">
	<?php echo $this->Navigation->render($backend_menu_for_layout['secondary']); ?>
</ul>
<?php endif; ?>
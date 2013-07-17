<?php
/**
 * @var CoreView $this
 */
if (isset($backend_menu_for_layout['main'])): ?>
<ul class="main-nav">
	<?php echo $this->Navigation->renderNested($backend_menu_for_layout['main']); ?>
</ul>
<?php endif; ?>
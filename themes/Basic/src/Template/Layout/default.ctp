<?php
/**
 * @var \Wasabi\Theme\Basic\View\BasicThemeView $this
 * @var string $title_for_layout
 * @var string $lang
 * @var array $layoutAttributes
 */
?>
<?= $this->element('page_header'); ?>
<div id="wrapper">
	<?= $this->element('header'); ?>
    <main id="content" class="container">
		<?= $this->fetch('content'); ?>
        <?= $this->contentArea('top') ?>
        <?= $this->contentArea('main') ?>
        <?= $this->contentArea('bottom') ?>
	</main>
	<?= $this->element('footer'); ?>
</div>
<?= $this->element('page_footer'); ?>

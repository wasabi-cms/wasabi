<?php
/**
 * @var \Wasabi\ThemeDefault\View\ThemeDefaultView $this
 * @var string $title_for_layout
 * @var string $lang
 * @var array $layoutAttributes
 */
?>
<?= $this->element('header'); ?>
<div id="wrapper">
	<?= $this->element('default/site_header'); ?>
    <main id="content">
		<?= $this->fetch('content'); ?>
        <?= $this->contentArea('main') ?>
	</main>
	<?= $this->element('default/site_footer'); ?>
</div>
<?= $this->element('footer'); ?>

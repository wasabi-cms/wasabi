<?php
/**
 * @var \WasabiTheme\Basic\View\BasicThemeView $this
 * @var \Wasabi\Cms\Model\Entity\Page $page
 */
?>
<?= $this->element('page_header'); ?>
<div id="wrapper">
	<?= $this->element('header'); ?>
    <main id="content">
        <article class="page-<?= $page->id ?>">
            <?= $this->fetch('content'); ?>
            <?= $this->contentArea('top') ?>
            <?= $this->contentArea('main') ?>
            <?= $this->contentArea('bottom') ?>
        </article>
	</main>
	<?= $this->element('footer'); ?>
</div>
<?= $this->element('page_footer'); ?>

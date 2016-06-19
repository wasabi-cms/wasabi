<?php
/**
 * @var \WasabiTheme\Basic\View\BasicThemeView $this
 * @var \Wasabi\Cms\Model\Entity\Page $page
 * @var string $title_for_layout
 * @var string $lang
 * @var array $layoutAttributes
 */
?>
<?= $this->element('html_head'); ?>
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
<?= $this->element('html_foot'); ?>

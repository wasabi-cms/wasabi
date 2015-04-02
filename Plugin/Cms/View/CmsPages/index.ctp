<?php
/**
 * @var CmsView $this
 * @var array $pages
 * @var array $closedPages
 */

$this->Html->setTitle(__d('cms', 'CMS Pages') . '<span class="lang">' . Configure::read('Wasabi.content_language.locale') . '</span>');
$this->Html->addAction(
	$this->Html->backendLink('<i class="icon-plus"></i>', 'cms/pages/add', array('class' => 'add', 'title' => __d('cms', 'Add a new Page'), 'escape' => false))
);

$this->start('Cms.requireJs');
echo 'wasabi.cms.pages();';
$this->end();

?>
<div class="list-header row">
	<div class="span7"><?php echo __d('cms', 'Page <small class="layout">Layout</small> <small class="collection">Collection</small> <small class="collection-item">Item</small>') ?></div>
	<div class="span2 center"><?php echo __d('cms', 'Live Edit') ?></div>
	<div class="span2 center"><?php echo __d('cms', 'Status') ?></div>
	<div class="span2 center"><?php echo __d('cms', 'Preview') ?></div>
	<div class="span1 center"><?php echo __d('cms', 'Sort') ?></div>
	<div class="span2 center"><?php echo __d('cms', 'Actions') ?></div>
</div>
<ul id="pages" class="list-content" data-reorder-url="<?php echo $this->Html->getBackendUrl('cms/pages/reorder', true, 'false') ?>">
	<?php echo $this->CmsPage->renderTree($pages, $closedPages, Configure::read('Wasabi.content_language.id')); ?>
</ul>

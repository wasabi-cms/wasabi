<?php
/**
 * @var CmsView $this
 * @var array $pageTypes
 * @var array $collections
 * @var array $collectionItems
 * @var array $layouts
 */

if ($this->params['action'] == 'add') {
	$this->Html->setTitle(__d('cms', 'Add a new Page') . '<span class="lang">' . Configure::read('Wasabi.content_language.locale') . '</span>');
} else {
	$this->Html->setTitle(__d('cms', 'Edit Page') . '<span class="lang">' . Configure::read('Wasabi.content_language.locale') . '</span>');
	$this->Html->setSubTitle($this->data['CmsPage']['name']);
}

echo $this->Form->create('CmsPage', array('novalidate' => true, 'class' => 'page-form'));
if ($this->params['action'] == 'edit') {
	echo $this->Form->input('id', array('type' => 'hidden'));

	$this->start('Cms.requireJs');
	echo 'wasabi.cms.routes();';
	$this->end();
}
?>
	<ul class="tabs row" data-tabify-id="page">
		<li class="active" data-tabify-target="general"><a href="javascript:void(0)"><?php echo __d('cms', 'General') ?></a></li>
		<li data-tabify-target="layout"><a href="javascript:void(0)"><?php echo __d('cms', 'Layout') ?></a></li>
		<li data-tabify-target="urls"<?php echo ($this->params['action'] === 'add') ? ' data-tabify-disabled="true"' : '' ?>><a href="javascript:void(0)"><?php echo __d('cms', 'URLs') ?></a></li>
	</ul>
	<div class="tab-content" data-tabify-tab="general" data-tabify-id="page">
		<?php
		echo $this->CForm->input('name', array('label' => __d('cms', 'Page Name')));
		echo $this->CForm->input('page_title', array('label' => __d('cms', 'Page Title')));
		echo $this->CForm->input('meta_description', array('label' => __d('cms', 'Meta Description'), 'type' => 'textarea', 'rows' => 2, 'info' => __d('cms', 'Describe in short what this page is about.')));
		echo $this->CForm->input('cached', array('options' => array('1' => __d('cms', 'Yes'), '0' => __d('cms', 'No')), 'label' => __d('cms', 'Enable Caching?')));
		?>
	</div>
	<div class="tab-content" data-tabify-tab="layout" data-tabify-id="page" style="display: none;">
		<?php echo $this->CForm->input('cms_layout', array('options' => $layouts, 'label' => __d('cms', 'Layout'), 'info' => __d('cms', 'Choose a layout for this page.'))); ?>
		<div class="form-row row">
			<label><?php echo __d('cms', 'Layout Fields') ?>:</label>
			<div class="field layout-attributes" data-change-url="<?php echo $this->Html->getBackendUrl('/cms/pages/attributes', true) ?>">
				<?php echo $this->element('cms_page_layout_attributes'); ?>
			</div>
		</div>
	</div>
<?php if ($this->params['action'] != 'add'): ?>
	<div class="tab-content" data-tabify-tab="urls" data-tabify-id="page" style="display: none;">
		<div class="form-row row">
			<label><?php echo __d('cms', 'Default URL slug') ?></label>
			<div class="field no-input">
				<?php
				if (isset($this->data['CmsPage']) && isset($this->data['CmsPage']['slug'])) {
					echo $this->data['CmsPage']['slug'];
				} else {
					echo '-';
				}
				?>
			</div>
		</div>
		<div class="form-row row">
			<label><?php echo __d('cms', 'URLs') ?>:</label>
			<div class="field routes">
				<?php echo $this->element('cms_page_routes'); ?>
			</div>
		</div>
	</div>
<?php endif; ?>
	<div class="form-controls">
		<?php
		echo $this->Form->button(__d('cms', 'Save'), array('div' => false, 'class' => 'button'));
		echo $this->Html->backendLink(__d('cms', 'Cancel'), '/cms/pages', array('class' => 'cancel'));
		?>
	</div>
<?php
echo $this->Form->end();

$layout = false;
$contentAreas = array();
if ($this->params['action'] === 'edit' && isset($this->data['CmsPage']['cms_layout'])) {
	$layout = CmsLayoutManager::getLayout($this->data['CmsPage']['cms_layout']);
	$contentAreas = $layout->getContentAreas();
}

if ($layout !== false && !empty($contentAreas)): ?>
	<ul class="tabs row mtop" data-tabify-id="content-areas">
		<?php $i = 0; foreach ($contentAreas as $contentAreaId => $contentAreaName): ?>
			<li<?php echo ($i === 0) ? ' class="active"' : '' ?> data-tabify-target="<?php echo $contentAreaId ?>"><a href="javascript:void(0)"><?php echo $contentAreaName ?></a></li>
			<?php $i++; endforeach; ?>
	</ul>
	<?php $i = 0; foreach ($contentAreas as $contentAreaId => $contentAreaName): ?>
		<div class="tab-content" data-tabify-tab="<?php echo $contentAreaId ?>" data-tabify-id="content-areas"<?php echo ($i > 0) ? ' style="display: none;"' : '' ?>>
			blub <?php echo $i ?>
		</div>
		<?php $i++; endforeach; ?>
<?php endif; ?>
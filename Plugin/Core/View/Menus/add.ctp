<?php
App::uses('MenuItem', 'Core.Model');
/**
 * @var CoreView $this
 * @var array $menuItems
 */

if ($this->params['action'] === 'add') {
	$this->Html->setTitle(__d('core', 'Add a new Menu'));
} else {
	$this->Html->setTitle(__d('core', 'Edit Menu'));
	$this->Html->setSubTitle($this->data['Menu']['name']);
}

echo $this->Form->create('Menu', array('class' => 'no-top-section', 'novalidate' => true));

if ($this->params['action'] == 'edit') {
	echo $this->Form->input('id', array('type' => 'hidden'));

	$this->start('Core.requireJs');
	echo 'wasabi.core.menuItems();';
	$this->end();
}

echo $this->CForm->input('name', array('label' => __d('core', 'Menu Name')));
?>
	<div class="form-row row">
		<label><?php echo __d('core', 'Menu Items') ?>:</label>
		<div class="field<?php echo ($this->params['action'] === 'add') ? ' no-input' : ''; ?>">
			<?php if ($this->params['action'] === 'edit'): ?>
			<div class="msg-box info"><?php echo __d('core', 'Tip: The maximum nesting level is <strong>2</strong>.') ?></div>
			<div class="list-header row">
				<div class="span10"><?php echo __d('core', 'Menu Item') ?></div>
				<div class="span2 center"><?php echo __d('core', 'Status') ?></div>
				<div class="span2 center"><?php echo __d('core', 'Sort') ?></div>
				<div class="span2 center"><?php echo __d('core', 'Actions') ?></div>
			</div>
			<ul id="menu-items" class="list-content" data-reorder-url="<?php echo $this->Html->getBackendUrl('/menus/reorder_items', true, 'false') ?>">
				<?php
				if ($menuItems) {
					echo $this->CMenu->renderMenuTree($menuItems);
				} else {
					echo '<li class="no-items center">' . __d('core', 'This Menu has no items yet.') . '</li>';
				}
				?>
			</ul>
			<div class="bottom-links">
				<?php echo $this->Html->backendLink(__d('core', 'Add a new Menu Item'), '/menus/add_item/' . $this->data['Menu']['id']) ?>
			</div>
			<?php else: ?>
			<?php echo __d('core', 'You can start adding Menu Items after you created the Menu.') ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="form-controls">
		<?php
		echo $this->Form->button(__d('core', 'Save'), array('div' => false, 'class' => 'button'));
		echo $this->Html->backendLink(__d('core', 'Cancel'), '/menus');
		?>
	</div>
<?php echo $this->Form->end(); ?>

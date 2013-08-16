<?php
/**
 * @var CoreView $this
 * @var array $menu
 * @var array $menus
 * @var array $parentItems
 * @var array $types
 */

if ($this->params['action'] === 'add_item') {
	$this->CHtml->setTitle(__d('core', 'Add a new Menu Item'));
} else {
	$this->CHtml->setTitle(__d('core', 'Edit Menu Item'));
	$this->CHtml->setSubTitle($this->data['MenuItem']['name']);
}

$this->CHtml->addAction(
	$this->CHtml->backendLink(__d('core', 'Back to %s Menu', array($menu['Menu']['name'])), '/menus/edit/' . $menu['Menu']['id'], array('class' => 'no-icon'))
);

echo $this->Form->create('MenuItem', array('novalidate' => true));

if ($this->params['action'] == 'edit_item') {
	echo $this->Form->input('id', array('type' => 'hidden'));
}

echo $this->CForm->input('name', array('label' => __d('core', 'Menu Item Name')));
echo $this->CForm->input('menu_id', array('label' => __d('core', 'Menu'), 'options' => $menus));
echo $this->CForm->input('parent_id', array('label' => __d('core', 'Parent Item'), 'options' => $parentItems, 'empty' => __d('core', '-- None --')));
echo $this->CForm->input('item', array('label' => __d('core', 'Link To'), 'options' => $types, 'empty' => __d('core', 'Please choose ...')));
echo $this->Form->input('type', array('type' => 'hidden'));

if (!isset($this->data['MenuItem']['type'])) {
	$this->request->data['MenuItem']['type'] = '';
}
?>
	<div class="form-row">
		<label><?php echo __d('core', 'Link Options') ?>:</label>
		<div class="field link-options">
			<div data-type="empty"<?php echo ($this->data['MenuItem']['type'] === '') ? ' class="active"' : '' ?>>-</div>
			<div data-type="<?php echo MenuItem::TYPE_EXTERNAL_LINK ?>"<?php echo ($this->data['MenuItem']['type'] === MenuItem::TYPE_EXTERNAL_LINK) ? ' class="active"' : '' ?>>
				<?php
				$options = array('label' => false, 'type' => 'text', 'disabled' => 'disabled');
				if ($this->data['MenuItem']['type'] === MenuItem::TYPE_EXTERNAL_LINK) {
					unset($options['disabled']);
				}
				echo $this->Form->label('external_link', __d('core', 'Link'));
				echo $this->Form->input('external_link', $options);
				?>
			</div>
			<div data-type="<?php echo MenuItem::TYPE_OBJECT ?>"<?php echo ($this->data['MenuItem']['type'] === MenuItem::TYPE_OBJECT) ? ' class="active"' : '' ?>>
				<?php
				$options = array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled');
				if ($this->data['MenuItem']['type'] === MenuItem::TYPE_OBJECT) {
					unset($options['disabled']);
				}
				echo $this->Form->input('foreign_model' ,$options);
				echo $this->Form->input('foreign_id'    ,$options);
				echo $this->Form->input('plugin'        ,$options);
				echo $this->Form->input('controller'    ,$options);
				echo $this->Form->input('action'        ,$options);
				echo $this->Form->input('params'        ,$options);
				echo $this->Form->input('query'         ,$options);
				echo __d('core', 'This link type has no custom options.');
				?>
			</div>
			<div data-type="<?php echo MenuItem::TYPE_ACTION ?>"<?php echo ($this->data['MenuItem']['type'] === MenuItem::TYPE_ACTION) ? ' class="active"' : '' ?>>
				<?php
				$options = array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled');
				if ($this->data['MenuItem']['type'] === MenuItem::TYPE_ACTION) {
					unset($options['disabled']);
				}
				echo $this->Form->input('plugin'     ,$options);
				echo $this->Form->input('controller' ,$options);
				echo $this->Form->input('action'     ,$options);
				echo $this->Form->input('params'     ,$options);
				echo $this->Form->input('query'      ,$options);
				?>
			</div>
			<div data-type="<?php echo MenuItem::TYPE_CUSTOM_ACTION ?>"<?php echo ($this->data['MenuItem']['type'] === MenuItem::TYPE_CUSTOM_ACTION) ? ' class="active"' : '' ?>>
				<?php
				$options = array('label' => false, 'disabled' => 'disabled');
				if ($this->data['MenuItem']['type'] === MenuItem::TYPE_CUSTOM_ACTION) {
					unset($options['disabled']);
				}
				echo $this->Form->label('plugin'     ,__d('core', 'Plugin'));
				echo $this->Form->input('plugin'     ,$options);
				echo $this->Form->label('controller' ,__d('core', 'Controller'));
				echo $this->Form->input('controller' ,$options);
				echo $this->Form->label('action'     ,__d('core', 'Action'));
				echo $this->Form->input('action'     ,$options);
				echo $this->Form->label('params'     ,__d('core', 'Params'));
				echo $this->Form->input('params'     ,$options + array('type' => 'text'));
				echo $this->Form->label('query'      ,__d('core', 'Query'));
				echo $this->Form->input('query'      ,$options + array('type' => 'text'));
				?>
			</div>
		</div>
	</div>
	<div class="form-controls">
		<?php
		echo $this->Form->button('<span>' . __d('core', 'Save') . '</span>', array('div' => false, 'class' => 'button'));
		echo $this->CHtml->backendLink(__d('core', 'Cancel'), '/menus/edit/' . $menu['Menu']['id']);
		?>
	</div>
<?php echo $this->Form->end(); ?>
<?php
App::uses('MenuItem', 'Core.Model');
/**
 * @var CoreView $this
 * @var array $menuItems
 */

if ($this->params['action'] === 'add') {
	$this->CHtml->setTitle(__d('core', 'Add a new Menu'));
} else {
	$this->CHtml->setTitle(__d('core', 'Edit Menu'));
	$this->CHtml->setSubTitle($this->data['Menu']['name']);
}

echo $this->Form->create('Menu', array('novalidate' => true));

if ($this->params['action'] == 'edit') {
	echo $this->Form->input('id', array('type' => 'hidden'));
}

echo $this->CForm->input('name', array('label' => __d('core', 'Menu Name')));
?>
<div class="form-row row">
	<label><?php echo __d('core', 'Menu Items') ?>:</label>
	<div class="field">
		<table id="menu-items" class="list is-sortable valign-middle">
			<thead>
			<tr>
				<th class="t14"><?php echo __d('core', 'Menu Item') ?></th>
				<th class="t2 center"><?php echo __d('core', 'Actions') ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if (!empty($this->data['MenuItem'])) {
				foreach ($this->data['MenuItem'] as $key => $value) {
					?>
					<tr>
						<td>
							<div class="row">
								<div class="span8">
									<?php
									echo $this->Form->input('MenuItem.' . $key . '.id', array('type' => 'hidden'));
									echo $this->Form->input('MenuItem.' . $key . '.delete', array('type' => 'hidden'));
									echo $this->Form->input('MenuItem.' . $key . '.position', array('type' => 'hidden'));
									echo $this->Form->input('MenuItem.' . $key . '.name', array('label' => __d('core', 'Name')));
									?>
								</div>
								<div class="span8">
									<?php
									echo $this->Form->input('MenuItem.' . $key . '.item', array('label' => __d('core', 'Type'), 'options' => $menuItems, 'empty' => __d('core', 'Please choose an item...'), 'class' => 'menu-item-select full'));
									echo $this->Form->input('MenuItem.' . $key . '.type', array('type' => 'hidden'));
									?>
								</div>
							</div>
							<?php
							if ($value['type'] === MenuItem::TYPE_EXTERNAL_LINK) {
								echo '<div class="active row" data-type="' . MenuItem::TYPE_EXTERNAL_LINK . '">';
								echo $this->Form->label('MenuItem.' . $key . '.external_link', __d('core', 'Link'));
								echo $this->Form->input('MenuItem.' . $key . '.external_link', array('label' => false, 'type' => 'text', 'id' => false));
								echo '</div>';
							} else {
								echo '<div class="row" data-type="' . MenuItem::TYPE_EXTERNAL_LINK . '">';
								echo $this->Form->label('MenuItem.' . $key . '.external_link', __d('core', 'Link'));
								echo $this->Form->input('MenuItem.' . $key . '.external_link', array('label' => false, 'type' => 'text', 'id' => false, 'disabled' => 'disabled'));
								echo '</div>';
							}

							if ($value['type'] === MenuItem::TYPE_OBJECT) {
								echo '<div class="active row" data-type="' . MenuItem::TYPE_OBJECT . '">';
								echo $this->Form->input('MenuItem.' . $key . '.foreign_model' ,array('type' => 'hidden', 'id' => false));
								echo $this->Form->input('MenuItem.' . $key . '.foreign_id'    ,array('type' => 'hidden', 'id' => false));
								echo $this->Form->input('MenuItem.' . $key . '.plugin'        ,array('type' => 'hidden', 'id' => false));
								echo $this->Form->input('MenuItem.' . $key . '.controller'    ,array('type' => 'hidden', 'id' => false));
								echo $this->Form->input('MenuItem.' . $key . '.action'        ,array('type' => 'hidden', 'id' => false));
								echo $this->Form->input('MenuItem.' . $key . '.params'        ,array('type' => 'hidden', 'id' => false));
								echo $this->Form->input('MenuItem.' . $key . '.query'         ,array('type' => 'hidden', 'id' => false));
								echo '</div>';
							} else {
								echo '<div class="row" data-type="' . MenuItem::TYPE_OBJECT . '">';
								echo $this->Form->input('MenuItem.' . $key . '.foreign_model' ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->input('MenuItem.' . $key . '.foreign_id'    ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->input('MenuItem.' . $key . '.plugin'        ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->input('MenuItem.' . $key . '.controller'    ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->input('MenuItem.' . $key . '.action'        ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->input('MenuItem.' . $key . '.params'        ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->input('MenuItem.' . $key . '.query'         ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo '</div>';
							}

							if ($value['type'] === MenuItem::TYPE_ACTION) {
								echo '<div class="active row" data-type="' . MenuItem::TYPE_ACTION . '">';
								echo $this->Form->input('MenuItem.' . $key . '.plugin'     ,array('type' => 'hidden', 'id' => false));
								echo $this->Form->input('MenuItem.' . $key . '.controller' ,array('type' => 'hidden', 'id' => false));
								echo $this->Form->input('MenuItem.' . $key . '.action'     ,array('type' => 'hidden', 'id' => false));
								echo $this->Form->input('MenuItem.' . $key . '.params'     ,array('type' => 'hidden', 'id' => false));
								echo $this->Form->input('MenuItem.' . $key . '.query'      ,array('type' => 'hidden', 'id' => false));
								echo '</div>';
							} else {
								echo '<div class="row" data-type="' . MenuItem::TYPE_ACTION . '">';
								echo $this->Form->input('MenuItem.' . $key . '.plugin'     ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->input('MenuItem.' . $key . '.controller' ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->input('MenuItem.' . $key . '.action'     ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->input('MenuItem.' . $key . '.params'     ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->input('MenuItem.' . $key . '.query'      ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
								echo '</div>';
							}

							if ($value['type'] === MenuItem::TYPE_CUSTOM_ACTION) {
								echo '<div class="active row" data-type="' . MenuItem::TYPE_CUSTOM_ACTION . '">';
								echo $this->Form->label('MenuItem.' . $key . '.plugin'     ,__d('core', 'Plugin'));
								echo $this->Form->input('MenuItem.' . $key . '.plugin'     ,array('label' => false, 'id' => false));
								echo $this->Form->label('MenuItem.' . $key . '.controller' ,__d('core', 'Controller'));
								echo $this->Form->input('MenuItem.' . $key . '.controller' ,array('label' => false, 'id' => false));
								echo $this->Form->label('MenuItem.' . $key . '.action'     ,__d('core', 'Action'));
								echo $this->Form->input('MenuItem.' . $key . '.action'     ,array('label' => false, 'id' => false));
								echo $this->Form->label('MenuItem.' . $key . '.params'     ,__d('core', 'Params'));
								echo $this->Form->input('MenuItem.' . $key . '.params'     ,array('label' => false, 'id' => false, 'type' => 'text'));
								echo $this->Form->label('MenuItem.' . $key . '.query'      ,__d('core', 'Query'));
								echo $this->Form->input('MenuItem.' . $key . '.query'      ,array('label' => false, 'id' => false, 'type' => 'text'));
								echo '</div>';
							} else {
								echo '<div class="row" data-type="' . MenuItem::TYPE_CUSTOM_ACTION . '">';
								echo $this->Form->label('MenuItem.' . $key . '.plugin'     ,__d('core', 'Plugin'));
								echo $this->Form->input('MenuItem.' . $key . '.plugin'     ,array('label' => false, 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->label('MenuItem.' . $key . '.controller' ,__d('core', 'Controller'));
								echo $this->Form->input('MenuItem.' . $key . '.controller' ,array('label' => false, 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->label('MenuItem.' . $key . '.action'     ,__d('core', 'Action'));
								echo $this->Form->input('MenuItem.' . $key . '.action'     ,array('label' => false, 'id' => false, 'disabled' => 'disabled'));
								echo $this->Form->label('MenuItem.' . $key . '.params'     ,__d('core', 'Params'));
								echo $this->Form->input('MenuItem.' . $key . '.params'     ,array('label' => false, 'id' => false, 'type' => 'text', 'disabled' => 'disabled'));
								echo $this->Form->label('MenuItem.' . $key . '.query'      ,__d('core', 'Query'));
								echo $this->Form->input('MenuItem.' . $key . '.query'      ,array('label' => false, 'id' => false, 'type' => 'text', 'disabled' => 'disabled'));
								echo '</div>';
							}
							?>
						</td>
						<td class="actions center">
							<?php
							echo $this->Html->link(__d('core', 'sort'), 'javascript:void(0)', array('title' => __d('core', 'Change the position of this Menu Item'), 'class' => 'wicon-sort sort'));
							echo $this->Html->link(__d('core', 'delete'), 'javascript:void(0)', array('title' => __d('core', 'Delete this Menu Item'), 'class' => 'wicon-remove remove-item'));
							?>
						</td>
					</tr>
				<?php
				}
			}
			?>
			<tr class="new">
				<td>
					<div class="row">
						<div class="span8">
							<?php
							echo $this->Form->input('MenuItem.{UID}.position', array('type' => 'hidden'));
							echo $this->Form->input('MenuItem.{UID}.name', array('label' => __d('core', 'Name')));
							?>
						</div>
						<div class="span8">
							<?php
							echo $this->Form->input('MenuItem.{UID}.item', array('label' => __d('core', 'Type'), 'options' => $menuItems, 'empty' => __d('core', 'Please choose an item...'), 'class' => 'menu-item-select full'));
							echo $this->Form->input('MenuItem.{UID}.type', array('type' => 'hidden'));
							?>
						</div>
					</div>
					<?php
					echo '<div data-type="' . MenuItem::TYPE_EXTERNAL_LINK . '">';
					echo $this->Form->label('MenuItem.{UID}.external_link', __d('core', 'Link'));
					echo $this->Form->input('MenuItem.{UID}.external_link', array('label' => false, 'type' => 'text', 'id' => false, 'disabled' => 'disabled'));
					echo '</div>';

					echo '<div data-type="' . MenuItem::TYPE_OBJECT . '">';
					echo $this->Form->input('MenuItem.{UID}.foreign_model' ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->input('MenuItem.{UID}.foreign_id'    ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->input('MenuItem.{UID}.plugin'        ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->input('MenuItem.{UID}.controller'    ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->input('MenuItem.{UID}.action'        ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->input('MenuItem.{UID}.params'        ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->input('MenuItem.{UID}.query'         ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo '</div>';

					echo '<div data-type="' . MenuItem::TYPE_ACTION . '">';
					echo $this->Form->input('MenuItem.{UID}.plugin'     ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->input('MenuItem.{UID}.controller' ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->input('MenuItem.{UID}.action'     ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->input('MenuItem.{UID}.params'     ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->input('MenuItem.{UID}.query'      ,array('type' => 'hidden', 'id' => false, 'disabled' => 'disabled'));
					echo '</div>';

					echo '<div data-type="' . MenuItem::TYPE_CUSTOM_ACTION . '">';
					echo $this->Form->label('MenuItem.{UID}.plugin'     ,__d('core', 'Plugin'));
					echo $this->Form->input('MenuItem.{UID}.plugin'     ,array('label' => false, 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->label('MenuItem.{UID}.controller' ,__d('core', 'Controller'));
					echo $this->Form->input('MenuItem.{UID}.controller' ,array('label' => false, 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->label('MenuItem.{UID}.action'     ,__d('core', 'Action'));
					echo $this->Form->input('MenuItem.{UID}.action'     ,array('label' => false, 'id' => false, 'disabled' => 'disabled'));
					echo $this->Form->label('MenuItem.{UID}.params'     ,__d('core', 'Params'));
					echo $this->Form->input('MenuItem.{UID}.params'     ,array('label' => false, 'id' => false, 'type' => 'text', 'disabled' => 'disabled'));
					echo $this->Form->label('MenuItem.{UID}.query'      ,__d('core', 'Query'));
					echo $this->Form->input('MenuItem.{UID}.query'      ,array('label' => false, 'id' => false, 'type' => 'text', 'disabled' => 'disabled'));
					echo '</div>';
					?>
				</td>
				<td class="actions center">
					<?php
					echo $this->Html->link(__d('core', 'sort'), 'javascript:void(0)', array('title' => __d('core', 'Change the position of this Menu Item'), 'class' => 'wicon-sort sort'));
					echo $this->Html->link(__d('core', 'delete'), 'javascript:void(0)', array('title' => __d('core', 'Delete this Menu Item'), 'class' => 'wicon-remove remove-item'));
					?>
				</td>
			</tr>
			</tbody>
		</table>
		<div class="bottom-links">
			<?php echo $this->Html->link(__d('core', 'Add a new Menu Item'), '#', array('class' => 'add-item')) ?>
		</div>
	</div>
</div>
<div class="form-controls">
	<?php
	echo $this->Form->button('<span>' . __d('core', 'Save') . '</span>', array('div' => false, 'class' => 'button'));
	echo $this->CHtml->backendLink(__d('core', 'Cancel'), '/menus');
	?>
</div>
<?php echo $this->Form->end(); ?>
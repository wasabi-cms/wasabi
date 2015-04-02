<?php
/**
 * @var CoreView 	$this
 * @var integer		$level
 * @var integer		$key
 * @var array 		$menuItem
 */
?>
<div class="row">
	<div class="span10">
		<div class="row">
			<div class="spacer">&nbsp;</div>
			<?php echo $this->Html->backendLink($menuItem['name'], '/menus/edit_item/' . $menuItem['id']); ?>
		</div>
	</div>
	<div class="span2 center">active</div>
	<div class="span2 center">
		<a href="javascript:void(0)" class="wicon-move move" title="<?php echo __d('core', 'Change the position of this Menu Item') ?>">move</a>
	</div>
	<div class="span2 center actions">
		<?php
		$options = array(
			'class' => 'wicon-add',
			'title' => __d('core', 'Add a child to this Menu Item')
		);
		if ($level > 2) {
			$options['class'] .= ' hide';
		}
		echo $this->Html->backendLink(__d('core', 'add parent'), '/menus/add_item/' . $this->request->data['Menu']['id'] . '/' . $menuItem['id'], $options);
		echo $this->Html->backendConfirmationLink(__d('core', 'delete'), '/menus/delete_item/' . $menuItem['id'], array(
				'class' => 'wicon-remove',
				'title' => __d('cms', 'Delete this Menu Item'),
				'confirm-title' => __d('cms', 'Delete Menu Item'),
				'confirm-message' => __d('cms', 'Do you really want to delete the Menu Item <strong>%s</strong>?', array($menuItem['name']))
			));
		?>
	</div>
</div>
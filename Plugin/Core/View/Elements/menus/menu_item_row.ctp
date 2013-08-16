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
			<?php echo $this->CHtml->backendLink($menuItem['name'], '/menus/edit_item/' . $menuItem['id']); ?>
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
		echo $this->CHtml->backendLink(__d('core', 'add parent'), '/menus/add_item/' . $this->request->data['Menu']['id'] . '/' . $menuItem['id'], $options);
		echo $this->Html->link(__d('core', 'delete'), 'javascript:void(0)', array('title' => __d('core', 'Delete this Menu Item'), 'class' => 'wicon-remove remove-item'));
		?>
	</div>
</div>
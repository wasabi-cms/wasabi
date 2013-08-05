<?php
/**
 * @var CoreView $this
 * @var array $plugins
 */

$this->CHtml->setTitle(__d('core', 'Permissions'));
$this->CHtml->addAction(
	$this->CHtml->backendLink('<i class="icon-refresh"></i>', '/permissions/sync', array('title' => __d('core', 'Synchronize Permissions'), 'class' => 'reload', 'escape' => false))
);

echo $this->Form->create('GroupPermission', array('url' => '/' . $this->backendPrefix . '/permissions/update'));
?>
<table class="list permissions valign-middle">
	<thead>
	<tr>
		<th class="t5"><?php echo __d('core', 'Controller') ?></th>
		<th class="t5"><?php echo __d('core', 'Action') ?></th>
		<th class="t4"><?php echo __d('core', 'Permissions') ?></th>
		<th class="t2 center"><?php echo __d('core', 'Update') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$rowCount = 1;
	$i = 1;
	foreach($plugins as $plugin => $controllers): ?>
		<tr class="plugin">
			<td colspan="4"><?php echo $plugin ?></td>
		</tr>
		<?php
		foreach ($controllers as $controller => $actions):
			$actionCount = 1;
			foreach ($actions as $action => $groups):
				$classes = array();
				if (($rowCount % 2) == 0) {
					$classes[] = 'even';
				}
				if ($actionCount === count($actions)) {
					$classes[] = 'last-action';
				}
				$classes = !empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '';
		?>
		<tr<?php echo $classes ?>>
			<?php if ($actionCount === 1): ?>
			<td class="controller" rowspan="<?php echo count($actions) ?>"><?php echo $controller ?></td>
			<?php endif; ?>
			<td class="action"><?php echo $action ?></td>
			<td>
				<?php foreach ($groups as $groupId => $group): ?>
				<div>
					<input id="GroupPermission<?php echo $i ?>Id" type="hidden" value="<?php echo $group['permission_id'] ?>" name="data[GroupPermission][<?php echo $i ?>][id]">
					<input id="GroupPermission<?php echo $i ?>Allowed_" type="hidden" value="0" name="data[GroupPermission][<?php echo $i ?>][allowed]">
					<input id="GroupPermission<?php echo $i ?>Allowed" type="checkbox" value="1" name="data[GroupPermission][<?php echo $i ?>][allowed]"<?php echo $group['allowed'] ? 'checked="checked"' : '' ?>>
					<label for="GroupPermission<?php echo $i ?>Allowed"><?php echo $group['name'] ?></label>
				</div>
				<?php $i++; endforeach; ?>
			</td>
			<td class="center valign-middle">
				<button class="single-submit button small" type="submit"><span><?php echo __d('core', 'Update') ?></span></button>
			</td>
		</tr>
		<?php
				$actionCount++;
				$rowCount++;
			endforeach;
		endforeach;
	endforeach;
	?>
	</tbody>
</table>
<div class="form-controls">
	<?php
	echo $this->Form->button('<span>' . __d('core', 'Update all') . '</span>', array('div' => false, 'class' => 'button'));
	echo $this->CHtml->backendLink(__d('core', 'Cancel'), '/permissions');
	?>
</div>
<?php echo $this->Form->end(); ?>
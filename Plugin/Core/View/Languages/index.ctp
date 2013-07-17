<?php
/**
 * @var CoreView $this
 * @var array $languages
 */

$this->CHtml->setTitle(__d('core', 'Languages'));

$this->CHtml->addAction(
	$this->CHtml->backendLink('<i class="icon-plus"></i>', '/languages/add', array('class' => 'add', 'title' => __d('core', 'Add a new Language'), 'escape' => false))
);

echo $this->Form->create('Language', array('url' => array('plugin' => 'core', 'controller' => 'languages', 'action' => 'sort')));
?>
<table id="languages" class="list">
	<thead>
	<tr>
		<th class="t1 center">ID</th>
		<th class="t4"><?php echo __d('core', 'Language') ?></th>
		<th class="t2"><?php echo __d('core', 'Locale') ?></th>
		<th class="t2"><?php echo __d('core', 'ISO') ?></th>
		<th class="t2"><?php echo __d('core', 'lang') ?></th>
		<th class="t3"><?php echo __d('core', 'Availability') ?></th>
		<th class="t2 center"><?php echo __d('core', 'Actions') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 1;
	foreach($languages as $lang) {
		$class = ($i % 2 == 0) ? ' class="even"' : '';
		?>
		<tr<?php echo $class ?>>
			<td class="right">
				<?php
				echo $this->Form->input('Language.'.$i.'.id', array('type' => 'hidden', 'value' => $lang['Language']['id']));
				echo $this->Form->input('Language.'.$i.'.position', array('type' => 'hidden', 'value' => $lang['Language']['position'], 'class' => 'position'));
				echo $lang['Language']['id'];
				?>
			</td>
			<td><?php echo $this->CHtml->backendLink($lang['Language']['name'], '/languages/edit/' . $lang['Language']['id'], array('title' => __d('core', 'Edit this Language'))) ?></td>
			<td><?php echo $lang['Language']['locale'] ?></td>
			<td><?php echo $lang['Language']['iso'] ?></td>
			<td><?php echo $lang['Language']['lang'] ?></td>
			<td>
				<?php
				$avail_class = '';
				if ($lang['Language']['available_at_frontend'] === true) {
					$avail_class = ' label-info';
				}
				?>
				<span class="label<?php echo $avail_class; ?>">Frontend</span>
				<?php
				$avail_class = '';
				if ($lang['Language']['available_at_backend'] === true) {
					$avail_class = ' label-info';
				}
				?>
				<span class="label<?php echo $avail_class; ?>">Backend</span>
			</td>
			<td class="actions center">
				<?php
				echo $this->Html->link('<i class="icon-resize-vertical"></i>', 'javascript:void(0)', array('title' => __d('core', 'Change the position of this Language'), 'class' => 'sort', 'escape' => false));
				if (!in_array($lang['Language']['id'], array(1, 2))) {
					echo $this->CHtml->backendConfirmationLink('<i class="icon-remove"></i>', '/languages/delete/' . $lang['Language']['id'], array(
						'title' => __d('core', 'Delete this Language'),
						'class' => 'remove',
						'confirm-message' => __d('core', 'Delete language <strong>%s</strong> ?', array($lang['Language']['name'])),
						'confirm-title' => __d('core', 'Deletion Confirmation'),
						'escape' => false
					));
				}
				?>
			</td>
		</tr>
		<?php
		$i++;
	}
	?>
	</tbody>
</table>
<?php echo $this->Form->end(); ?>
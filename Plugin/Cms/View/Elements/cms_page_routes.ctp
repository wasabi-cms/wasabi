<?php
/**
 * @var array $routes
 * @var array $routeTypes
 */
$error = false;
if ($this->request->is('ajax')) {
	$error = ($this->Session->read('Message.flash.params.class') === 'error') && ($this->params['action'] !== 'delete');
	echo $this->Session->flash();
}
?>
<table class="list routes valign-middle" data-add-route-url="<?php echo $this->Html->getBackendUrl('/cms/routes/add', true) ?>">
	<thead>
	<tr>
		<th class="t11"><?php echo __d('cms', 'URL') ?></th>
		<th class="t3 center"><?php echo __d('cms', 'Route Type') ?></th>
		<th class="t2 center"><?php echo __d('cms', 'Actions') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($routes as $r): ?>
		<tr>
			<td><?php echo ($r['Route']['redirect_to'] === null) ? '<strong>' . $r['Route']['url'] . '</strong>' : $r['Route']['url'] ?></td>
			<td class="center"><?php
				if ($r['Route']['redirect_to'] === null) {
					echo '<strong>' . __d('cms', 'Default Route') . '</strong>';
				} else {
					echo $this->Html->backendConfirmationLink(__d('cms', 'Redirect Route'), '/cms/routes/make_default/' . $r['Route']['id'], array(
						'title' => __d('cms', 'Make this Route the Default Route.'),
						'confirm-message' => __d('cms', 'Do you really want to make<br/><strong>%s</strong><br/>the new default route for this page?', array($r['Route']['url'])),
						'confirm-title' => __d('cms', 'Make Default Route'),
						'ajax' => true,
						'notify' => '.field.routes',
						'event' => 'makeDefaultRoute'
					));
				}
				?></td>
			<td class="actions center">
				<?php
				echo $this->Html->backendConfirmationLink(__d('cms', 'delete'), '/cms/routes/delete/' . $r['Route']['id'], array(
					'class' => 'wicon-remove',
					'title' => __d('cms', 'Delete this Route'),
					'confirm-message' => __d('cms', 'Do you really want to delete Route <strong>%s</strong>?', array($r['Route']['url'])),
					'confirm-title' => __d('cms', 'Deletion Confirmation'),
					'ajax' => true,
					'notify' => '.field.routes',
					'event' => 'deleteRoute'
				));
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	<tr class="new-route<?php echo $error ? ' valign-top' : '' ?>">
		<td>
			<?php echo $this->Form->input('Route.url', array('label' => false, 'type' => 'text')); ?>
		</td>
		<td class="center">
			<?php echo $this->Form->input('Route.type', array('label' => false, 'options' => $routeTypes, 'default' => (count($routes) >= 1) ? Route::TYPE_REDIRECT_ROUTE : Route::TYPE_DEFAULT_ROUTE)); ?>
		</td>
		<td class="actions center">
			<?php echo $this->Form->button(__d('cms', 'Submit'), array('div' => false, 'class' => 'button small')); ?>
		</td>
	</tr>
	</tbody>
</table>
<small><?php echo __d('cms', 'The different URLs define all locations this page will be available on.') ?></small>
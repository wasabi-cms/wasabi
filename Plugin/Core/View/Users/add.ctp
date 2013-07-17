<?php
/**
 * @var CoreView $this
 * @var array $groups
 * @var array $languages
 */

if ($this->params['action'] === 'add') {
	$this->CHtml->setTitle(__d('core', 'Add a new User'));
} else {
	$this->CHtml->setTitle(__d('core', 'Edit User'));
	$this->CHtml->setSubTitle($this->data['User']['username']);
}

echo $this->Form->create('User');

if ($this->params['action'] == 'edit') {
	echo $this->Form->input('id', array('type' => 'hidden'));
}

echo $this->CForm->input('username', array('label' => __d('core', 'Username')));
if ($this->params['action'] !== 'add') {
	echo $this->CForm->input('password_unencrypted', array('label' => __d('core', 'Password'), 'class' => 'text', 'type' => 'password', 'info' => __d('core', 'To change the user\'s password fill in both password fields. Otherwise leave those fields empty.')));
} else {
	echo $this->CForm->input('password_unencrypted', array('label' => __d('core', 'Password'), 'class' => 'text', 'type' => 'password'));
}
echo $this->CForm->input('password_confirmation', array('label' => __d('core', 'Password Confirmation'), 'class' => 'text', 'type' => 'password'));
echo $this->CForm->input('group_id', array('label' => __d('core', 'Group'), 'options' => $groups));
echo $this->CForm->input('language_id', array('label' => __d('core', 'Backend Language'), 'options' => $languages));
if ($this->params['action'] !== 'add' && isset($this->data['User']['id']) && $this->data['User']['id'] != 1 && $this->data['User']['id'] != Authenticator::get('User.id')) {
	echo $this->CForm->input('active', array('label' => __d('core', 'this user account is active'), 'type' => 'checkbox', 'title' => __d('core', 'Active')));
}
?>
<div class="form-controls fixed">
	<?php
	echo $this->Form->button(__d('core', 'Save'), array('div' => false, 'class' => 'button green primary'));
	echo $this->CHtml->backendLink(__d('core', 'Cancel'), '/users', array('class' => 'button danger'));
	?>
</div>
<?php echo $this->Form->end(); ?>
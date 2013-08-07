<?php
/**
 * @var CoreView $this
 */
?>
<h1>Backend Login</h1>
<?php
echo $this->Session->flash();
echo $this->Form->create('User', array('url' => array('plugin' => 'core', 'controller' => 'users', 'action' => 'login')));
echo '<div class="form-content">';
echo $this->Form->input('User.username', array('label' => __d('core', 'Username').':', 'class' => 'big'));
echo $this->Form->input('User.password', array('label' => __d('core', 'Password').':', 'class' => 'big'));
echo $this->Form->input('User.remember', array(
	'label' => __d('core', 'Remember me for 2 weeks'),
	'type' => "checkbox"
));
echo '</div>';
echo '<div class="form-controls">';
echo $this->Form->button('<span>' . __d('core', 'Login') . '</span>', array('class' => 'button'));
echo '</div>';
echo $this->Form->end();
?>
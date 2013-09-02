<header class="row">
	<ul class="row">
		<li><a class="toggle-nav" href="javascript:void(0)"><i class="icon-reorder"></i></a></li>
		<li><?php echo $this->CHtml->backendUnprotectedLink('wasabi', '/', array('class' => 'brand'))?></li>
		<?php echo $this->element('Core.menus/top_nav'); ?>
		<?php echo $this->element('Core.menus/user'); ?>
		<?php echo $this->element('Core.menus/language'); ?>
	</ul>
</header>
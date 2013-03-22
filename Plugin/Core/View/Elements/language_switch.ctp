<?php
/**
 * @var CoreView $this
 */
?>
<ul class="lang-switch">
	<?php
	foreach (Configure::read('Languages.frontend') as $lang) {
		$class = '';
		if ($lang['id'] == Configure::read('Wasabi.content_language.id')) {
			$class = ' class="active"';
		}
		echo "<li${class}>" . $this->CHtml->backendLink($lang['locale'], '/languages/change/' . $lang['id']) . "</li>";
	}
	$languages = Configure::read('Languages');
	?>
</ul>
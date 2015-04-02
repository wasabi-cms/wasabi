<?php
/**
 * @var CoreView $this
 */
?>
<li class="lang-switch">
	<ul>
		<?php
		$frontendLanguages = Configure::read('Languages.frontend');
		if (!empty($frontendLanguages)) {
			foreach (Configure::read('Languages.frontend') as $lang) {
				$class = '';
				if ($lang['id'] == Configure::read('Wasabi.content_language.id')) {
					$class = ' class="active"';
				}
				echo "<li${class}>" . $this->Html->backendLink($lang['locale'], '/languages/change/' . $lang['id']) . "</li>";
			}
		}
		?>
	</ul>
</li>
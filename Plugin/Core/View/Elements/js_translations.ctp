<?php
$translations = array();
$available_translations = WasabiEventManager::trigger(new stdClass(), 'Backend.JS.Translations.load');

foreach ($available_translations['Backend.JS.Translations.load'] as $key => $tgroup) {
	$translations = array_merge($translations, $tgroup);
}

$i = 1;
$len = count($translations);
?>
<script type="text/javascript">
	window.wasabi = window.wasabi || {};
	window.wasabi.translations = window.wasabi.translations || {};
	window.wasabi.translations = {
		<?php
		foreach ($translations as $key => $val) {
			echo '"' . $key . '": "' . $val . '"';
			if ($i < $len) {
				echo ',';
			}
			$i++;
		}
		?>
	};
</script>

<?php
$translations = array();
$available_translations = WasabiEventManager::trigger(new stdClass(), 'Backend.JS.Translations.load');

foreach ($available_translations['Backend.JS.Translations.load'] as $key => $tgroup) {
	$translations = array_merge($translations, $tgroup);
}

$out =  '<script type="text/javascript">var wasabiTranslations = {';

$i = 1;
foreach ($translations as $key => $val) {
	$out .= '"'. $key .'": "'. $val .'"';
	if ($i < count($translations)) {
		$out .= ',';
	}
	$i++;
}

$out .= '};</script>';

echo $out;
?>
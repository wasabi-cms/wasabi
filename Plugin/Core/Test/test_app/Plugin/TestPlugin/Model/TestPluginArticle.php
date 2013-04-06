<?php

class TestPluginArticle extends Model {

	public $useDbConfig = 'test';

	public $actsAs = array(
		'Core.Relatable'
	);

	public $belongsTo = array(
		'TestPluginCategory' => array(
			'className' => 'TestPlugin.TestPluginCategory'
		)
	);

	public $recursive = -1;

}

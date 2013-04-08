<?php

class TestPluginArticle extends Model {

	public $useDbConfig = 'test';

	public $actsAs = array(
		'Core.Relatable',
		'Core.Translatable' => array(
			'fields' => array(
				'title',
				'content'
			)
		)
	);

	public $belongsTo = array(
		'TestPluginCategory' => array(
			'className' => 'TestPlugin.TestPluginCategory'
		)
	);

	public $recursive = -1;

}

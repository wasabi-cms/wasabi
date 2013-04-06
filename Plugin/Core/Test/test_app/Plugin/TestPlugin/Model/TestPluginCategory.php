<?php

class TestPluginCategory extends Model {

	public $useDbConfig = 'test';

	public $actsAs = array(
		'Core.Relatable'
	);

	public $hasMany = array(
		'TestPluginPost' => array(
			'className' => 'TestPlugin.TestPluginPost'
		),
		'TestPluginArticle' => array(
			'className' => 'TestPlugin.TestPluginArticle'
		)
	);

	public $recursive = -1;

}

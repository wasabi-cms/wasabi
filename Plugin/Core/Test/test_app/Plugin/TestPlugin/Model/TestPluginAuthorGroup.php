<?php

class TestPluginAuthorGroup extends Model {

	public $useDbConfig = 'test';

	public $actsAs = array(
		'Core.Relatable'
	);

	public $hasMany = array(
		'TestPluginAuthor' => array(
			'className' => 'TestPlugin.TestPluginAuthor'
		)
	);

	public $recursive = -1;

}

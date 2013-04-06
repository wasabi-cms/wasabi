<?php

class TestPluginAuthor extends Model {

	public $useDbConfig = 'test';

	public $actsAs = array(
		'Core.Relatable'
	);

	public $belongsTo = array(
		'TestPluginAuthorGroup' => array(
			'className' => 'TestPlugin.TestPluginAuthorGroup'
		)
	);

	public $hasMany = array(
		'TestPluginPost' => array(
			'className' => 'TestPlugin.TestPluginPost'
		)
	);

	public $recursive = -1;

}

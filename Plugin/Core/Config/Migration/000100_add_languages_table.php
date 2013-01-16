<?php
class AddLanguagesTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'languages' => array(
					'id' => array(
						'type' => 'integer',
						'key' => 'primary'
					),
					'name' => array(
						'type' => 'string',
						'null' => false
					),
					'locale' => array(
						'type' => 'string',
						'length' => 2,
						'null' => false
					),
					'iso' => array(
						'type' => 'string',
						'length' => 3,
						'null' => false
					),
					'lang' => array(
						'type' => 'string',
						'length' => 5,
						'null' => false
					),
					'available_at_frontend' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => 0
					),
					'available_at_backend' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => 0
					),
					'position' => array(
						'type' => 'integer',
						'null' => true,
						'default' => null
					),
					'created' => array(
						'type' => 'datetime',
						'null' => false
					),
					'modified' => array(
						'type' => 'datetime',
						'null' => false
					),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						)
					)
				)
			)
		),
		'down' => array(
			'drop_table' => array(
				'languages'
			)
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
		if ($direction === 'up') {
			$language = ClassRegistry::init('Core.Language');
			// English
			$language->create(array(
				'name' => 'English',
				'locale' => 'en',
				'iso' => 'eng',
				'lang' => 'en-US',
				'available_at_frontend' => true,
				'available_at_backend' => true,
				'position' => 1
			));
			$language->save();
			// Deutsch
			$language->create(array(
				'name' => 'Deutsch',
				'locale' => 'en',
				'iso' => 'eng',
				'lang' => 'en-US',
				'available_at_frontend' => true,
				'available_at_backend' => true,
				'position' => 2
			));
			$language->save();
		}
		return true;
	}
}

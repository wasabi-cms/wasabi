<?php
class AddRoutesTable extends CakeMigration {

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
				'routes' => array(
					'id' => array(
						'type' => 'integer',
						'key' => 'primary'
					),
					'url' => array(
						'type' => 'text',
						'null' => false
					),
					'plugin' => array(
						'type' => 'string',
						'length' => 255,
						'null' => false,
					),
					'controller' => array(
						'type' => 'string',
						'length' => 255,
						'null' => false
					),
					'action' => array(
						'type' => 'string',
						'length' => 255,
						'null' => false
					),
					'params' => array(
						'type' => 'text',
						'null' => false
					),
					'redirect_to' => array(
						'type' => 'integer',
						'null' => true
					),
					'status_code' => array(
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
				'routes'
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
		return true;
	}
}

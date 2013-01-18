<?php
class AddLoginTokensTable extends CakeMigration {

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
				'login_tokens' => array(
					'id' => array(
						'type' => 'integer',
						'key' => 'primary'
					),
					'user_id' => array(
						'type' => 'integer',
						'null' => false
					),
					'token' => array(
						'type' => 'string',
						'length' => 32,
						'null' => false
					),
					'expires' => array(
						'type' => 'datetime',
						'null' => false
					),
					'created' => array(
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
				'login_tokens'
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

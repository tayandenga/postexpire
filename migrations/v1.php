<?php
/**
*
* @package Post expire
* @copyright (c) 2018 Codefather
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace codefather\postexpire\migrations;

class v1 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\dev');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'posts' => array(
					'post_expire' => array('USINT', 0)
				)
			)
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('post_expire_last_run', 0, true)),
			array('permission.add', array('f_post_expire', false))
		);
	}
}

<?php
/**
*
* @package Post expire
* @copyright (c) 2018 Codefather
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*
*/

namespace codefather\postexpire\cron\task;

/**
* @ignore
*/
use phpbb\cron\task\base as task_base;
use phpbb\config\config;
use codefather\postexpire\includes\helper;
use phpbb\log\log;
use phpbb\user;

class post_expire extends task_base
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var codefather\postexpire\includes\helper */
	protected $helper;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Cron task constructor.
	 *
	 * @param \codefather\postexpire\includes\helper		$helper
	 * @param \phpbb\user									$user
	 * @param \phpbb\log\log								$log
	 *
	 * @return void
	 */
	public function __construct(config $config, helper $helper, log $log, user $user)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->log = $log;
		$this->user = $user;
	}

	/**
	 * Check if the cron task can be executed.
	 *
	 * @return bool
	 */
	public function is_runnable()
	{
		return true;
	}

	/**
	 * Returns whether this cron task should run now, because enough time
	 * has passed since it was last run (60 seconds).
	 *
	 * @return bool
	 */
	public function should_run()
	{
		return $this->config['post_expire_last_run'] < (time() - 60);
	}

	/**
	 * Execute the cron task.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->config->set('post_expire_last_run', time(), false);
		$count = $this->helper->post_expire();

		if($count > 0) {
			$this->log->add(
				'admin',
				$this->user->data['user_id'],
				$this->user->ip,
				'LOG_POST_EXPIRE',
				false,
				array($count)
			);
		}
	}
}

<?php
/**
*
* @package Post expire
* @copyright (c) 2018 Codefather
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*
*/

namespace codefather\postexpire\includes;

/**
* @ignore
*/
use phpbb\db\driver\factory as database;
use phpbb\content_visibility;



class helper
{
	/** @var \phpbb\db\driver\factory */
	protected $db;

	/** @var \phpbb\content_visibility */
	protected $cv;

	/**
	 * Constructor of the helper class.
	 *
	 * @param \phpbb\db\driver\factory $db
	 * @param \phpbb\content_visibility $cv
	 *
	 * @return void
	 */
	public function __construct(database $db, content_visibility $cv)
	{
		$this->db = $db;
		$this->cv = $cv;
	}


	/**
	 * Cleanup all expiring posts (and topics if $is_starter is true).
	 *
	 * @return array
	 */
	public function post_expire()
	{
		$sql = 'SELECT `p`.`post_id`, `p`.`topic_id`, `p`.`forum_id`, `p`.`post_visibility`, `t`.`topic_first_post_id`, `t`.`topic_last_post_id`
			FROM `' . POSTS_TABLE . '` p
			LEFT JOIN `' . TOPICS_TABLE . '` t ON `p`.`topic_id` = `t`.`topic_id`
			WHERE `p`.`post_expire` > 0 AND UNIX_TIMESTAMP() >= (`p`.`post_time` + `p`.`post_expire` * 86400);';

		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$posts = array();
		foreach ($rowset as $post) {
			if ($post['post_visibility'] == ITEM_DELETED) {
				continue;
			}

			array_push($posts, $post['post_id']);
			if ($post['post_id'] == $post['topic_first_post_id']) {
				$this->cv->set_topic_visibility(ITEM_DELETED, $post['topic_id'], $post['forum_id'], 0, time(), '', true);
			} elseif ($post['post_id'] == $post['topic_last_post_id']) {
				$this->cv->set_post_visibility(ITEM_DELETED, $post['post_id'], $post['topic_id'], $post['forum_id'], 0, time(), '', false, true);
			} else {
				$this->cv->set_post_visibility(ITEM_DELETED, $post['post_id'], $post['topic_id'], $post['forum_id'], 0, time(), '', false, false);
			}
		}

		if (count($posts) == 0) {
			return 0;
		}

		$sql = 'UPDATE `' . POSTS_TABLE . '` SET `post_expire` = 0 WHERE `post_id` IN (' . implode(',', $posts) . ')';
		$this->db->sql_query($sql);
		return (int)$this->db->sql_affectedrows();
	}
}

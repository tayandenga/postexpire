<?php
/**
*
* @package Post expire
* @copyright (c) 2018 Codefather
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*
*/

namespace codefather\postexpire\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth					$auth				Auth object
	* @param \phpbb\request\request				$request			Request object
	* @param \phpbb\template\template           $template       	Template object
	* @param \phpbb\user                        $user           	User object
	* @access public
	*/
	public function __construct(
			\phpbb\auth\auth $auth,
			\phpbb\request\request $request,
			\phpbb\template\template $template,
			\phpbb\user $user)
	{
		$this->auth = $auth;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions'							=> 'add_permission',
			'core.posting_modify_template_vars'			=> 'posting_modify_template_vars',
			'core.viewtopic_modify_page_title'			=> 'viewtopic_modify_page_title',
			'core.posting_modify_submit_post_before'	=> 'posting_modify_submit_post_before',
			'core.posting_modify_message_text'			=> 'posting_modify_message_text',
			'core.submit_post_modify_sql_data'			=> 'submit_post_modify_sql_data',
			'core.viewtopic_post_rowset_data'			=> 'viewtopic_post_rowset_data',
			'core.viewtopic_modify_post_row'			=> 'viewtopic_modify_post_row',
		);
	}

	/**
	* Add administrative permissions to manage forums
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions['f_post_expire'] = array('lang' => 'ACL_F_post_expire', 'cat' => 'post');
		$event['permissions'] = $permissions;
	}

	public function posting_modify_template_vars($event)
	{
		$page_data = $event['page_data'];
		if ($this->auth->acl_get('f_post_expire', $event['forum_id'])) {
			$page_data['S_POST_EXPIRE'] = true;
			$page_data['post_expire'] = $this->request->variable('post_expire', $event['post_data']['post_expire'], true);

			$this->user->add_lang_ext('codefather/postexpire', 'common');
			$event['page_data'] = $page_data;
		}
	}

	public function viewtopic_modify_page_title($event)
	{
		$this->user->add_lang_ext('codefather/postexpire', 'common');
		if ($this->auth->acl_get('f_post_expire', $event['forum_id'])) {
			$this->template->assign_vars(array(
				'S_POST_EXPIRE' => true
			));
		}
	}

	public function posting_modify_submit_post_before($event)
	{
		$event['data'] = array_merge($event['data'], array(
			'post_expire' => $this->request->variable('post_expire', 0, true),
		));
	}

	public function submit_post_modify_sql_data($event)
	{
		$sql_data = $event['sql_data'];
		$sql_data[POSTS_TABLE]['sql']['post_expire'] = $event['data']['post_expire'];
		$event['sql_data'] = $sql_data;
	}

	public function posting_modify_message_text($event)
	{
		$event['post_data'] = array_merge($event['post_data'], array(
			'post_expire' => $this->request->variable('post_expire', 0, true),
		));
	}

	public function viewtopic_post_rowset_data($event)
	{
		$rowset_data = $event['rowset_data'];
		$rowset_data['post_expire'] = $event['row']['post_expire'];
		$event['rowset_data'] = $rowset_data;
	}

	public function viewtopic_modify_post_row($event)
	{
		$post_row = $event['post_row'];
		if(!$post_row['S_POST_DELETED'] && $event['row']['post_expire'] > 0) {
			$post_row['S_POST_EXPIRE'] = true;
			$post_row['POST_EXPIRE'] = $this->user->format_date($event['row']['post_time'] + ($event['row']['post_expire'] * 86400));
			$event['post_row'] = $post_row;
		}
	}
}

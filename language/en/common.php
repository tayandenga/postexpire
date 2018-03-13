<?php
/**
*
* @package Post expire
* @copyright (c) 2018 Codefather
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'POST_EXPIRE'				=> 'Expire post in'
	'POST_EXPIRE_NOT'			=> 'Do not expire',
	'POST_EXPIRE_DAY'			=> 'day',
	'POST_EXPIRE_DAYS'			=> 'days',
	'POST_EXPIRE_EXPLAIN'		=> 'If post is the first post in the topic, topic will be deleted as well.',
	'POST_EXPIRE_INFO'			=> 'Post expires'
));

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
	'POST_EXPIRE'				=> 'Wygaś post za',
	'POST_EXPIRE_NOT'			=> 'Nie wygaszaj',
	'POST_EXPIRE_DAY'			=> 'dzień',
	'POST_EXPIRE_DAYS'			=> 'dni',
	'POST_EXPIRE_EXPLAIN'		=> 'Jeżeli post jest pierwszym postem w temacie, temat również zostanie usunięty.',
	'POST_EXPIRE_INFO'			=> 'Post wygasa'
));
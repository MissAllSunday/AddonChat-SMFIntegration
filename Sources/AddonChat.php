<?php

/**
 * AddonChat Integration mod (SMF)
 *
 * @package Addonchat Integration
 * @author Suki <missallsunday@simplemachines.org>
 * @copyright 2012 Jessica González
 * @license http://www.mozilla.org/MPL/ MPL 2.0
 *
 * @version 1.0
 */

/**
 * Version: MPL 2.0
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 * If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 */

/* No direct Access! */
if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Autoload, checks if the file exists inside the AddonChat folder, this is called automatically
 *
 * @access public
 * @param string the Class name
 * @return boolean false if there is no file to load
 */
function AddonChat_autoloader($class_name)
{
	global $sourcedir;

	$file_path = $sourcedir .'/AddonChat/'. $class_name .'.php';

	if(file_exists($file_path))
		require_once($file_path);

	else
		return false;
}

spl_autoload_register('AddonChat_autoloader');

/**
 * Wrapper function, SMF cannot handle static methods being called via a variable: $static_method();
 *
 * @access public
 * @return void
 */
function AddonChat_SubActions_Wrapper(){AddonChat::subActions();};

/**
 * The main class
 * @package Addonchat Integration
 * @subpackage classes
 */
class AddonChat
{
	protected $_user;
	protected $_data = array();
	protected $_rows = array();
	public static $name = 'AddonChat';
	protected $serverUrl = 'http://clientx.addonchat.com/queryaccount.php';
	public static $permissions = array('can_msg', 'can_action', 'allow_pm', 'allow_room_create', 'allow_avatars', 'can_random', 'allow_bbcode', 'allow_color', 'msg_scroll', 'filter_shout', 'filter_profanity', 'filter_word_replace', 'can_nick', 'can_kick', 'can_affect_admin', 'can_grant', 'can_cloak', 'can_see_cloak', 'login_cloaked', 'can_ban', 'can_ban_subnet', 'can_system_speak', 'can_silence', 'can_fnick', 'can_launch_website', 'can_transfer', 'can_join_nopw', 'can_topic', 'can_close', 'can_ipquery', 'can_geo_locate', 'can_query_ether', 'can_clear_screen', 'can_clear_history', 'allow_room_create',);

	/**
	 * @var string The name of the DB table
	 * @access public
	 */
	public static $_dbTableName = 'addonchat';

	public function __construct(){}

	public static function tools()
	{
		return AddonChatTools::getInstance();
	}

	/**
	 * Checks if the mod is enable, if it is, checks if the user has access to the chat
	 *
	 * @access public
	 * @global array $txt The text array
	 * @static
	 * @param boolean if true, check if the user is allowed to see the chat
	 * @return void
	 */
	public static function isEnable($skip_view_permission = false)
	{
		global $txt;

		loadLanguage(self::$name);

		/* Guest cannot see the chat */
		is_not_guest(self::tools()->getText('no_guest'));

		if (!self::tools()->enable('enable_general'))
			redirectexit();

		elseif ($skip_view_permission == false)
			isAllowedTo(self::$name .'_see_chat');
	}

	/**
	 * Checks for RAS, it starts with a basic check for any settings, then check if you are capable of using RAS and lastly if you had enable it
	 *
	 * @access public
	 * @global string $boardurl the forum url without index.php
	 * @static
	 * @param boolean if true, the method will return a text string depending on the error, if false, it will only return a boolean
	 * @return mixed either a boolean or a string
	 */
	public static function enableRAS($returnText = false)
	{
		global $boardurl;

		/* Load what we need */
		$tools = self::tools();
		$gSettings = $tools->globalSettingAll();

		/* If there is no data, tell the user to connect to the server first */
		if (empty($gSettings))
			return $returnText ? $tools->getText('connect_with_server') : true;

		/* We have data, lets do some more checks */
		elseif (!empty($gSettings) && is_array($gSettings))
		{
			/* You should be able to use RAS */
			if (empty($gSettings['remote_auth_capable']))
				return $returnText ? $tools->getText('remote_auth_capable') : true;

			/* If you are capable of using RAS, you must enable it first */
			if (empty($gSettings['remote_auth_enable']))
				return $returnText ? sprintf($tools->getText('enable_RAS'), $gSettings['control_panel_login'], $boardurl .'/ChatAuth.php') : true;
		}

		/* Its all good :)  */
		else
			return false;
	}

	/**
	 * The main function that loads the chat inside the action=chat page
	 *
	 * @access public
	 * @global array $context An array used to pass variables to the template
	 * @global string $scripturl the actual forum's full url
	 * @static
	 * @return void
	 */
	public static function main()
	{
		global $context, $scripturl;

		$tools = self::tools();

		/* Check */
		self::isEnable();

		loadTemplate(self::$name);

		$context['page_title'] =  $tools->getText('title_main');
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=chat',
			'name' => $tools->getText('title_main')
		);
		$context['canonical_url'] = $scripturl . '?action=chat';
		$context['sub_template'] = 'addonChat_main';
		$context['robot_no_index'] = true;
		$context[self::$name]['tools'] = $tools;
		$context[self::$name]['issue'] = false;

		/* Check if we can use RAS */
		$checkRAS = self::enableRAS(true);

		/* We got some issues, pass them to the template */
		if (!empty($checkRAS))
			$context[self::$name]['issue'] = $checkRAS;

		/* We are good to go */
		else
		{
			/* Get all the global settings */
			$context[self::$name]['global_settings'] = $tools->globalSettingAll();

			/* Server_id needs to be an int too */
			$context[self::$name]['global_settings']['server_id'] = preg_replace('[\D]', '', $context[AddonChat::$name]['tools']->globalSetting('server_name'));
		}
	}

	/**
	 * Action hook creates the action=chat actions which later is used to create a separate page inside the forum structure
	 *
	 * @access public
	 * @param array $actions passed by reference, holds all the actions in SMF until this method is called
	 * @static
	 * @return void
	 */
	public static function actions(&$actions)
	{
		$actions['chat'] = array(self::$name .'.php', self::$name .'::main');
	}

	/**
	 * Permissions hook creates the permissions checks used by the integration
	 *
	 * @access public
	 * @param array $permissionGroups passed by reference, holds all the permissions groups
	 * @param array $permissionList passed by reference, holds all the permissions lists
	 * @static
	 * @return void
	 */
	public static function permissions(&$permissionGroups, &$permissionList)
	{
		/* Name of the different permissions style */
		$permissionGroups['membergroup']['simple'] = array(self::$name .'_per_simple');
		$permissionGroups['membergroup']['classic'] = array(self::$name .'_per_classic');

		/* This is a general permission, see the chat */
		$permissionList['membergroup'][self::$name .'_see_chat'] = array(false, self::$name .'_per_classic', self::$name .'_per_simple');

		/* Get the permissions array */
		$temp = self::$permissions;

		/* Print specific permissions by user */
		foreach ($temp as $k)
			$permissionList['membergroup'][self::$name .'_'. $k] = array(false, self::$name .'_per_classic', self::$name .'_per_simple');
	}

	/**
	 * Button hook creates a Chat button on the main SMF menu
	 *
	 * @access public
	 * @param array $menu_buttons passed by reference, holds all the possible buttons on the menu.
	 * @static
	 * @return void
	 */
	public static function menu(&$menu_buttons)
	{
		global $scripturl;

		$tools = self::tools();

		/* Don't do anything if the mod is not enable */
		if (!$tools->enable('enable_general'))
			return;

		$connect = new AddonChatServer();
		$whos = $connect->whosChatting();

		$insert = $tools->enable('menu_position') ? $tools->getSetting('menu_position') : 'home';

		/* Let's add our button next to the user's selection...
		 * Thanks to SlammedDime (http://mattzuba.com) for the example */
		$counter = 0;
		foreach ($menu_buttons as $area => $dummy)
			if (++$counter && $area == $insert)
				break;

		$menu_buttons = array_merge(
			array_slice($menu_buttons, 0, $counter),
			array('chat' => array(
			'title' => $tools->getText('title_main') . ' ['. (!is_array($whos) ? '0' : $whos['number']) .']',
			'href' => $scripturl . '?action=chat',
			'show' => allowedTo(self::$name .'_see_chat'),
			'sub_buttons' => array(
				'chat_admin' => array(
					'title' => $tools->getText('settings_menu'),
					'href' => $scripturl . '?action=admin;area='. self::$name,
					'show' => allowedTo('admin_forum'),
					'sub_buttons' => array(),
				),
			),
		)),
			array_slice($menu_buttons, $counter)
		);
	}

	/**
	 * Builds the admin button via hooks
	 *
	 * @access public
	 * @static
	 * @param array The admin menu
	 * @return void
	 */
	public static function admin(&$admin_areas)
	{
		$tools = self::tools();

		$admin_areas['config']['areas'][self::$name] = array(
			'label' => $tools->getText('default_menu'),
			'file' => self::$name .'.php',
			'function' => 'AddonChat_SubActions_Wrapper',
			'icon' => 'posts.gif',
			'subsections' => array(
				'general' => array($tools->getText('general_settings')),
				'look' => array($tools->getText('look_settings'))
			),
		);
	}

	/**
	 * Creates the pages for the admin panel via hooks
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function subActions()
	{
		global $scripturl, $context, $sourcedir;

		/* We need this */
		require_once($sourcedir . '/ManageSettings.php');

		/* Get the text strings */
		$tools = self::tools();

		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $tools->getText('default_menu'),
			'description' => $tools->getText('admin_panel_desc'),
			'tabs' => array(
				'general' => array(),
				'look' => array()
			),
		);

		/* Set the page title */
		$context['page_title'] = $tools->getText('default_menu');

		$subActions = array(
			'general' => 'self::generalSettings',
			'look' => 'self::lookSettings'
		);

		loadGeneralSettingParameters($subActions, 'general');
		call_user_func($subActions[$_REQUEST['sa']]);
	}

	/**
	 * The General settings page
	 *
	 * @access public
	 * @static
	 * @param boolean
	 * @return void
	 */
	static function generalSettings($return_config = false)
	{
		global $scripturl, $txt, $context, $sourcedir, $boardurl;

		/* We need this */
		require_once($sourcedir . '/ManageServer.php');

		$tools = self::tools();

		/* Generate the settings */
		$config_vars = array(
			array('check', self::$name .'_enable_general', 'subtext' => $tools->getText('enable_general_sub')),
			array('int', self::$name .'_number_id', 'size' => 36, 'subtext' => $tools->getText('number_id_sub')),

			array('text', self::$name .'_pass', 'size' => 36, 'subtext' => $tools->getText('pass_sub')),
		);

		if ($return_config)
			return $config_vars;

		/* Set some settings for the page */
		$context['post_url'] = $scripturl . '?action=admin;area='. self::$name .';sa=general;save';
		$context['page_title'] = $tools->getText('default_menu');

		/* Get the global settings */
		$gSettings = $tools->globalSettingAll();

		/* If the user has successfully called the external site, lets tell them the next steps */
		if (!empty($gSettings))
			$context['settings_message'] =  sprintf($tools->getText('settings_message_true'), $gSettings['control_panel_login'], $boardurl .'/ChatAuth.php');

		/* No? then tell them how to connect */
		else
			$context['settings_message'] =  $tools->getText('settings_message_false');

		if (isset($_GET['server']))
		{
			$connect = new AddonChatServer();
			$connect->getAccount();

			redirectexit('action=admin;area='. self::$name);
		}

		if (isset($_GET['save']))
		{
			/* Save the settings */
			checkSession();

			saveDBSettings($config_vars);
			redirectexit('action=admin;area='. self::$name);
		}

		prepareDBSettingContext($config_vars);
	}

	/**
	 * The look settings page
	 *
	 * @access public
	 * @static
	 * @param boolean
	 * @return void
	 */
	static function lookSettings($return_config = false)
	{
		global$scripturl, $context, $sourcedir;

		/* We need this */
		require_once($sourcedir . '/ManageServer.php');

		$tools = self::tools();

		/* Set some settings for the page */
		$context['post_url'] = $scripturl . '?action=admin;area='. self::$name .';sa=look;save';
		$context['page_title'] = $tools->getText('default_menu');

		/* Generate the settings */
		$config_vars = array(
			array('check', self::$name .'_allow_avatar', 'subtext' => $tools->getText('allow_avatar_sub')),
			array('check', self::$name .'_show_chatusers_menu', 'subtext' => $tools->getText('show_chatusers_menu_sub')),
			array('check', self::$name .'_show_chatusers_boardIndex', 'subtext' => $tools->getText('show_chatusers_boardIndex_sub')),
			array('int', self::$name .'_max_msg_length', 'size' => 10, 'subtext' => $tools->getText('max_msg_length_sub')),
			array('select', self::$name .'_menu_position', array(
					'home' => $tools->getText('menu_home'),
					'help' => $tools->getText('menu_help'),
					'search' => $tools->getText('menu_search'),
					'login' => $tools->getText('menu_login'),
					'register' => $tools->getText('menu_register')
				),
				'subtext' => $tools->getText('menu_position_sub')
			),
		);

		if ($return_config)
			return $config_vars;

		/* Page settings */
		$context['post_url'] = $scripturl . '?action=admin;area='. self::$name .';sa=look;save';
		$context['page_title'] = $tools->getText('default_menu');

		/* Save */
		if (isset($_GET['save']))
		{
			checkSession();
			saveDBSettings($config_vars);
			redirectexit('action=admin;area=', self::$name ,';sa=look');
		}

		prepareDBSettingContext($config_vars);
	}
}
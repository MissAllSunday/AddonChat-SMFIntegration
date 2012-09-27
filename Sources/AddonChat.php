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

/*
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

if (!defined('SMF'))
	die('Hacking attempt...');

/* Autoload */
function __autoload($class_name)
{
	global $sourcedir;

	$file_path = $sourcedir .'/AddonChat/'. $class_name .'.php';

	if(file_exists($file_path))
		require_once($file_path);

	else
		return false;
}

/**
 * Wrapper function
 *
 * SMF cannot handle static methods being called via a variable: $static_method();
 */
function AddonChat_SubActions_Wrapper(){AddonChat::subActions();};

class AddonChat
{
	protected $_user;
	protected $_data = array();
	protected $_rows = array();
	public static $name = 'AddonChat';
	protected $serverUrl = 'http://clientx.addonchat.com/queryaccount.php';
	public static $permissions = array( 'see_chat', 'can_msg', 'can_action', 'allow_pm', 'allow_room_create', 'allow_avatars', 'can_random', 'allow_bbcode', 'allow_color', 'msg_scroll', 'filter_shout', 'filter_profanity', 'filter_word_replace', 'can_nick', 'can_kick', 'can_affect_admin', 'can_grant', 'can_cloak', 'can_see_cloak', 'login_cloaked', 'can_ban', 'can_ban_subnet', 'can_system_speak', 'can_silence', 'can_fnick', 'can_launch_website', 'can_transfer', 'can_join_nopw', 'can_topic', 'can_close', 'can_ipquery', 'can_geo_locate', 'can_query_ether', 'can_clear_screen', 'can_clear_history', 'allow_room_create', 'see_chat', );

	/**
	 * @var string The name of the DB table
	 * @access public
	 */
	public static $_dbTableName = 'addonchat';

	public function __construct()
	{
	}

	public static function tools()
	{
		return AddonChatTools::getInstance();
	}

	public static function main()
	{
		global $context, $scripturl;

		$tools = self::tools();

		loadTemplate(self::$name);

		$context['page_title'] =  $tools->getText('title_main');
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=chat',
			'name' => $tools->getText('title_main')
		);
		$context['canonical_url'] = $scripturl . '?action=chat';
		$context['sub_template'] = 'addonChat_main';
		$context['robot_no_index'] = true;

		/* Get all the global settings */
		$context[self::$name]['global_settings'] = $tools->globalSettingAll();
		$context[self::$name]['tools'] = $tools;
	}

	/* Action hook */
	public static function actions(&$actions)
	{
		$actions['chat'] = array(self::$name .'.php', self::$name .'::main');
	}

	/* Permissions hook */
	public static function permissions(&$permissionGroups, &$permissionList)
	{
		/* Name of the different permissions style */
		$permissionGroups['membergroup']['simple'] = array(self::$name .'_per_simple');
		$permissionGroups['membergroup']['classic'] = array(self::$name .'_per_classic');

		/* Get the permissions array */
		$temp = self::$permissions;

		/* Print specific permissions by user */
		foreach ($temp as $k)
			$permissionList['membergroup'][self::$name .'_'. $k] = array(false, self::$name .'_per_classic', self::$name .'_per_simple');
	}

	/* Button menu hook */
	public static function menu(&$menu_buttons)
	{
		global $scripturl;

		$tools = self::tools();

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
			'title' => $tools->getText('title_main'),
			'href' => $scripturl . '?action=chat',
			'show' => true,
			'sub_buttons' => array(),
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
			array('select', self::$name .'_menu_position', array(
					'home' => $tools->getText('menu_home'),
					'help' => $tools->getText('menu_help'),
					'search' => $tools->getText('menu_search'),
					'login' => $tools->getText('menu_login'),
					'register' => $tools->getText('menu_register')
				),
				'subtext' => $tools->getText('menu_position_sub')
			),
			array('select', self::$name .'_permission_style', array(
					'group' => $tools->getText('permission_style_group'),
					'individual' => $tools->getText('permission_style_individual'),
				),
				'subtext' => $tools->getText('permission_style_sub')
			),
			array('int', self::$name .'_max_msg_length', 'size' => 10, 'subtext' => $tools->getText('max_msg_length_sub')),
		);

		if ($return_config)
			return $config_vars;

		/* Set some settings for the page */
		$context['post_url'] = $scripturl . '?action=admin;area='. self::$name .';sa=general;save';
		$context['page_title'] = $tools->getText('default_menu');

		/* Get the global settings */
		$gSettings = $tools->globalSettingAll();

		/* If the user has sucesfully called the external site, lets tell them the next steps */
		if (!empty($gSettings))
			$context['settings_message'] =  sprintf($tools->getText('settings_message_true'), $gSettings['control_panel_login'], $boardurl .'/ChatAuth.php');

		/* No? then tell them how to connect */
		else
			$context['settings_message'] =  $tools->getText('settings_message_false');

		if (isset($_GET['server']))
		{
			$connect = new AddonChatServer();
			$connect->getAccount();

			redirectexit('action=admin;area=AddonChat');
		}

		if (isset($_GET['save']))
		{
			/* Save the settings */
			checkSession();

			saveDBSettings($config_vars);
			redirectexit('action=admin;area=AddonChat');
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

		/* Generate the settings */
		$config_vars = array(
			/* currently empty! */
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
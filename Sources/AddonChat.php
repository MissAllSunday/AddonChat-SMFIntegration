<?php

/**
 * AddonChat Integration mod (SMF)
 *
 * @package SMF
 * @author Suki <missallsunday@simplemachines.org>
 * @copyright 2012 Jessica Gonz�lez
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
	private $serverUrl = 'http://clientx.addonchat.com/queryaccount.php';

	public function __construct()
	{
	}

	protected static function tools()
	{
		global $sourcedir;

		require_once($sourcedir .'/'. self::$name .'/AddonChatTools.php');
		return AddonChatTools::getInstance();
	}

	/*
	 * Calls the external server to retrieve the server number and client ID
	 *
	 * This will be done just 1 time, the function will store the values on the DB
	 * @access public
	 * @return array An array containing the fetched values
	 */
	public function getAccount()
	{
		global $sourcedir;

		/* Set what we need */
		$tools = self::tools();
		$query = $tools->query();

		/* We need the password and the ID, lets check if we have it, if not tell the user to store it first */
		if (!$tools->enable('pass') || !$tools->enable('number_id'))
			fatal_lang_error(self::$name .'_no_pass_set', false);

		/* Requires a function in a source file far far away... */
		require_once($sourcedir .'/Subs-Package.php');

		/* Lets md5 the pass */
		$pass = md5($tools->getSetting('pass'));

		/* Build the url */
		$url = $this->serverUrl. '?id='. $tools->getSetting('number_id') .'&md5pw='. $pass;

		/* Attempts to fetch data from a URL, regardless of PHP's allow_url_fopen setting */
		$data = fetch_web_data($url);

		/* Oops, something went wrong, tell the user to try later */
		if ($data == null)
			fatal_lang_error(self::$name .'_error_fetching_server', false);

		/* We got something */
		$data = explode(PHP_EOL, $data);

		/* Cleaning */
		foreach ($data as $key => $value)
			if (trim($value) == '')
				unset($data[$key]);

		/* The server says no */
		if ($data[0] == '-1')
			fatal_lang_error(self::$name .'_error_from_server', false, array($data[2]));

		/* Make sure the data is what is supposed to be, $data[1] must match this regex: /\((.+)\)/ */
		if (preg_match('/\((.+)\)/', $data[1]))
		{
			/* Make a quick query to see if theres data already saved */
			$query->params(array(
				'rows' => '*',
			));
			$query->getData(null, false);
			$result = $query->dataResult();

			/* There is, so make an update */
			if (!empty($result))
			{
				/* Update the cache */
				$tools->killCache();

				$query->params(
					array(
						'set' => 'read = {int:read}',
					),
					array(
						'edition_code' => $data[0],
						'modules' => $data[1],
						'remote_auth_capable' => $data[2],
						'full_service' => $data[3],
						'expiration_date' => $data[4],
						'remote_auth_enable' => $data[5],
						'remote_auth_url' => $data[6],
						'server_name' => $data[7],
						'tcp_port' => $data[8],
						'control_panel_login' => $data[9],
						'chat_title' => $data[10],
						'product_code' => $data[11],
						'customer_code' => $data[12],
					)
				);
				$query->updateData();
			}

			/* No data, create the rows */
			else
				$query->insertData(
				array(
						'edition_code' => 'int',
						'modules' => 'string',
						'remote_auth_capable' => 'int',
						'full_service' => 'string',
						'expiration_date' => 'string',
						'remote_auth_enable' => 'int',
						'remote_auth_url' =>'string',
						'server_name' => 'string',
						'tcp_port' => 'string',
						'control_panel_login' => 'string',
						'chat_title' => 'string',
						'product_code' => 'string',
						'customer_code' => 'string',
				),
				$data,
				array(
					'customer_code',
				)
			);
		}
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
	}

	/* Action hook */
	public static function actions(&$actions)
	{
		$actions['chat'] = array(self::$name .'.php', self::$name .'::main');
	}

	/* Permissions hook */
	public static function permissions(&$permissionGroups, &$permissionList)
	{
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
	 * @param boolean
	 * @return void
	 */
	public static function subActions()
	{
		global $scripturl, $context, $sourcedir;

		/* We need this */
		require_once($sourcedir . '/ManageSettings.php');

		/* Get the text strings */
		$tools = self::tools();

		/* Set the page title */
		$context['page_title'] = $tools->getText('default_menu');

		$subActions = array(
			'general' => 'self::generalSettings',
			'look' => 'self::lookSettings'
		);

		loadGeneralSettingParameters($subActions, 'general');

		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $tools->getText('default_menu'),
			'description' => $tools->getText('admin_panel_desc'),
			'tabs' => array(
				'general' => array(),
				'look' => array()
			),
		);

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
		global $scripturl, $txt, $context, $sourcedir;

		/* We need this */
		require_once($sourcedir . '/ManageServer.php');

		$tools = self::tools();

		/* Generate the settings */
		$config_vars = array(
			array('check', self::$name .'_enable_general', 'subtext' => $tools->getText('enable_general_sub')),
			array('int', self::$name .'_number_id', 'size' => 36, 'subtext' => $tools->getText('number_id_sub')),
			array('text', self::$name .'_pass', 'size' => 36, 'subtext' => $tools->getText('pass_sub')),

			/* Ugly, I know */
			'',
			$tools->getText('server_call'),
		);

		if ($return_config)
			return $config_vars;

		/* Set some settings for the page */
		$context['post_url'] = $scripturl . '?action=admin;area='. self::$name .';sa=general;save';
		$context['page_title'] = $tools->getText('default_menu');

		if (isset($_GET['server']))
		{
			$connect = new self();
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
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

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Ohara Tools Class
 *
 * How to use:
 *
 * - Change the name of the class to something unique
 * - Set the $name property with your own mod name, $modSettings and $txt keys should use the same name followed by an underscore, example:
 *   $txt[MyMod_enable], $modSettings[MyMod_enable], etc.
 * - Load the file via __autoload() or some other method.
 * @package OharaTools
 */
class AddonChatTools
{
	/**
	 * @var object The unique instance of the class
	 * @access private
	 */
	private static $_instance;

	/**
	 * @var array An array containing all the settings founded by $this->extract()
	 * @see OharaTools::extract()
	 * @access protected
	 */
	protected $_settings = array();

	/**
	 * @var array An array containing all the txt strings founded by $this->extract()
	 * @see OharaTools::extract()
	 * @access protected
	 */
	protected $_text = array();

	/**
	 * @var string The pattern used to search the modsettings and txt arrays, should be: /identifier_/ this is defined with the value of $name
	 * @access protected
	 */
	protected $_pattern;

	/**
	 * @var array The DB tables used by the script
	 * @access protected
	 */
	protected static $_dbTables = array('edition_code', 'modules', 'remote_auth_capable', 'full_service', 'expiration_date', 'remote_auth_enable', 'remote_auth_url', 'server_name', 'tcp_port', 'control_panel_login', 'chat_title', 'product_code', 'customer_code');

	/**
	 * Initialize the extract() method and sets the pattern property using $name's value.
	 *
	 * @access protected
	 * @return void
	 */
	protected function __construct()
	{
		/* Set the name */
		$this->name = Addonchat::$name;

		/* Set the pattern property with $name's value */
		$this->_pattern = '/'. $this->name .'_/';

		$this->gSetting = array();

	}

	/**
	 * Set's a unique instance for the class.
	 *
	 * @access public
	 * @return object
	 */
	public static function getInstance()
	{
		if (!self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}

	/**
	 * Resets the unique instance for the class.
	 *
	 * @access public
	 * @return void
	 */
	public static function reset()
	{
		self::$_instance = NULL;
	}

	/*
	 * Cleans the old cache value
	 *
	 * Replace the existing cache data with a null value so SMF generates a new cache...
	 * @access public
	 * @param mixed $type the name of value(s) to be deleted
	 * @return void
	 */
	public function killCache()
	{
		cache_put_data(AddonChat::$name .':gSettings', '');
	}

	/**
	 * Performs a query to get the data from the addonchat table.
	 *
	 * @access public
	 * @return mixed
	 */
	public function extract()
	{
		global $smcFunc;

		/* This won't be updated that frecuently */
		if (($this->gSetting = cache_get_data(AddonChat::$name .':gSettings', 600)) == null)
		{
			$query = $smcFunc['db_query']('', '
				SELECT '. implode(',', self::$_dbTables) .'
				FROM {db_prefix}'. AddonChat::$_dbTableName,
				array()
			);

			while($row = $smcFunc['db_fetch_assoc']($query))
			{
				if (!empty($row))
					$this->gSetting = $row;

				else
					$this->gSetting = '';
			}

			/* Cache this beauty only if there is something worth to be saved */
			if (!empty($this->gSetting))
				cache_put_data(AddonChat::$name .':gSettings', $this->gSetting, 600);
		}
	}

	/**
	 * Return all the values from the global settings table.
	 *
	 * @access public
	 * @return mixed can be either an array of values or a boolean false
	 */
	public function globalSettingAll()
	{
		/* Get the global Settings */
		if (empty($this->gSetting))
			$this->extract();

		if (!empty($this->gSetting))
			return $this->gSetting;

		else
			return false;
	}

	/**
	 * Return the value from the global settings table.
	 *
	 * @param string the name of the key
	 * @access public
	 * @return mixed
	 */
	public function globalSetting($var)
	{
		/* Get the global Settings */
		$this->extract();

		if (!empty($this->gSetting[$var]))
			return $this->gSetting[$var];

		else
			return false;
	}

	/**
	 * Return true if the param value do exists on the $modSettings array, false otherwise.
	 *
	 * @param string the name of the key
	 * @access public
	 * @return bool
	 */
	public function enable($var)
	{
		global $modSettings;

		if (!empty($modSettings[$this->name .'_'. $var]))
			return true;

		else
			return false;
	}

	/**
	 * Get the requested array element.
	 *
	 * @param string the key name for the requested element
	 * @access public
	 * @return mixed
	 */
	public function getSetting($var)
	{
		global $modSettings;

		if (empty($var))
			return false;

		elseif (!empty($modSettings[$this->name .'_'. $var]))
			return $modSettings[$this->name .'_'. $var];

		else
			return false;
	}

	/**
	 * Get the requested array element.
	 *
	 * @param string the key name for the requested element
	 * @access public
	 * @return mixed
	 */
	public function getText($var)
	{
		global $txt;

		/* Load the mod's language file */
		loadLanguage($this->name);

		if (!empty($txt[$this->name .'_'. $var]))
			return $txt[$this->name .'_'. $var];
	}

	/* Load user specific data */
	public function loadData($user)
	{
		global $smcFunc, $scripturl, $txt;

		if (empty($user))
			return false;

		$select_columns = '
			IFNULL(lo.log_time, 0) AS is_online, mem.id_member, mem.member_name,
			mem.real_name, mem.date_registered, mem.id_post_group, mem.id_group, mem.passwd, mem.additional_groups, mg.online_color AS member_group_color, IFNULL(mg.group_name, {string:blank_string}) AS member_group';
		$select_tables = '
			LEFT JOIN {db_prefix}log_online AS lo ON (lo.id_member = mem.id_member)
			LEFT JOIN {db_prefix}membergroups AS pg ON (pg.id_group = mem.id_post_group)
			LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = mem.id_group)';

		$request = $smcFunc['db_query']('', '
			SELECT' . $select_columns . '
			FROM {db_prefix}members AS mem' . ($select_tables) . '
			'. (is_array($user) ? 'WHERE mem.real_name
					OR mem.member_name IN ({array_string:user})' : 'WHERE mem.real_name = {string:user}
				OR mem.member_name = {string:user}'),
			array(
				'user' => $user,
			)
		);

		$return = array();


		/* If $user is a string */
		if (!is_array($user))
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
				$return = $row;

			/* Append the primary group to the list of additonal groups */
			if (!empty($return['additional_groups']))
			{
				$return['additional_groups'] = explode(',', $return['additional_groups']);
				array_push($return['additional_groups'], $return['id_post_group'], $return['id_group']);
			}

			/* If there is no additional groups, use the primary and post group */
			else
				$return['additional_groups'] = array($return['id_post_group'], $return['id_group']);
		}

		/* Then we organize the returned data */
		else
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$return[$row['id_member']] = array(
					'id' => $row['id_member'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '" title="' . $txt['profile_of'] . ' ' . $row['real_name'] . '" style="color:'. $row['member_group_color'] .';">' . $row['real_name'] . '</a>',
					'username' => $row['member_name'],
					'name' => $row['real_name'],
				);
			}
		}

		$smcFunc['db_free_result']($request);

		return $return;
	}

	/* Custom method to check the users permissions */
	public static function loadPermissions($user_groups)
	{
		global $modSettings, $smcFunc, $sourcedir;

		if (empty($user_groups))
			return false;

		/* Must be an array */
		if (!is_array($user_groups))
			$user_groups = array($user_groups);

		$return = array();
		$removals = array();

		// Get the general permissions.
		$request = $smcFunc['db_query']('', '
			SELECT permission, add_deny
			FROM {db_prefix}permissions
			WHERE id_group IN ({array_int:member_groups})',
			array(
				'member_groups' => $user_groups,
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			if (empty($row['add_deny']))
				$removals[] = $row['permission'];
			else
				$return[] = $row['permission'];
		}

		$smcFunc['db_free_result']($request);

		return $return;
	}
}
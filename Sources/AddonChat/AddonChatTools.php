<?php

/**
 * AddonChat Integration mod (SMF)
 *
 * @package SMF
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

/**
 * Ohara Tools Class
 *
 * How to use:
 *
 * - Change the name of the class to something unique
 * - Set the $_name property with your own mod name, $modSettings and $txt keys should use the same name followed by an underscore, example:
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
	 * @var string The pattern used to search the modsettings and txt arrays, should be: /identifier_/ this is defined with the value of $_name
	 * @access protected
	 */
	protected $_pattern;

	/**
	 * Initialize the extract() method and sets the pattern property using $_name's value.
	 *
	 * @access protected
	 * @return void
	 */
	protected function __construct()
	{
		/* Set the name */
		$this->_name = Addonchat::$_name;

		/* Set the pattern property with $_name's value */
		$this->_pattern = '/'. $this->_name .'_/';

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
		cache_put_data(AddonChat::$_name .':gSettings', '');
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
		if (($this->gSetting = cache_get_data(AddonChat::$_name .':gSettings', 600)) == null)
		{
			$query = $smcFunc['db_query']('', '
				SELECT *
				FROM {db_prefix}'. AddonChat::$_dbTableName,
				array()
			);

			while($row = $smcFunc['db_fetch_assoc']($query))
				$this->gSetting = $row;

			/* Cache this beauty */
			cache_put_data(AddonChat::$_name .':gSettings', $this->gSetting, 600);
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

		if (!empty($modSettings[$this->_name .'_'. $var]))
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

		elseif (!empty($modSettings[$this->_name .'_'. $var]))
			return $modSettings[$this->_name .'_'. $var];

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
		loadLanguage($this->_name);

		if (!empty($txt[$this->_name .'_'. $var]))
			return $txt[$this->_name .'_'. $var];
	}
}
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

/**
 * AddonchatServer
 *
 * Makes call to the external Chat server
 * @package Addonchat Integration
 */
class AddonChatServer extends Addonchat
{
	/**
	 * Initialize the tools() method from AddonChatTools class.
	 *
	 * @access public
	 * @global string $sourcedir the path to the forum sources directory
	 * @global array $smcFunc holds the SMF DB layer
	 * @return void
	 */
	public function __construct()
	{
		global $sourcedir, $smcFunc;

		$this->_sourcedir = $sourcedir;
		$this->_smcFunc = $smcFunc;

		/* Call the parent */
		parent::__construct();

		/* We need to global settings */
		$this->_settings = parent::tools();
	}

	/**
	 * Tries to fetch the content of a given url
	 *
	 * @access protected
	 * @param string $url the url to call
	 * @return mixed either the page requested or a boolean false
	 */
	protected function fetch_web_data($url)
	{
		/* Safety first! */
		if (empty($url))
			return false;

		/* Requires a function in a source file far far away... */
		require_once($this->_sourcedir .'/Subs-Package.php');

		/* Send the result directly, we are gonna handle it on every case */
		return fetch_web_data($url);
	}

	/**
	 * Calls the external server to retrieve info about who is chatting
	 *
	 * Uses the SMF cache system
	 * @access public
	 * @return array An array containing the fetched values
	 */
	public function whosChatting()
	{
		global $memberContext;

		/* Get the global settings */
		$gSettings = $this->_settings->globalSettingAll();

		/* Set this as an empty array */
		$return = array();

		/* Built the url */
		$url = 'http://' . $gSettings['server_name'] . '/scwho.php?style=0&id=' . $this->_settings->getSetting('number_id') . '&port=' . $gSettings['tcp_port'] .'&roompw=' . md5($this->_settings->getSetting('pass'));

		/* Use the cache */
		if (($return = cache_get_data(parent::$name .':whoschatting', 30)) == null)
		{
			/* Attempts to fetch data from an URL, regardless of PHP's allow_url_fopen setting */
			$data = $this->fetch_web_data($url);

			/* Oops, something went wrong, tell the user to try later */
			if ($data == null)
				return $return = array();

			/* Get 1 user per line */
			$temp = explode("\n", $data);

			/* Clean and separate each user */
			foreach ($temp as $key => $value)
				$temp2[] = explode("\t", $value);

			/* Get the actual usernames */
			foreach($temp2 as $t)
				if (is_array($t))
					if (!empty($t[1]))
						$usernames[] = $t[1];

			/* Load the users info */
			$ids = loadMemberData($usernames, true, 'normal');
			$user = array();

			if (!empty($ids) && is_array($ids))
				foreach ($ids as $i)
				{
					loadMemberContext($i);
					$user[$i] = $memberContext[$i];
				}

			/* Append the data */
			$return['users'] = $user;

			/* Get the number of users */
			$return['number'] = count($temp);

			/* Cache this beauty */
			cache_put_data(parent::$name .':whoschatting', $return, 30);
		}

		/* Return the data */
		return $return;
	}

	/**
	 * Calls the external server to retrieve the server number and client ID
	 *
	 * This will be done just 1 time, the function will store the values on the DB
	 * @access public
	 * @return array An array containing the fetched values
	 */
	public function getAccount()
	{
		/* Set what we need */
		$tools = $this->_settings;

		/* We need the password and the ID, lets check if we have it, if not tell the user to store it first */
		if (!$tools->enable('pass') || !$tools->enable('number_id'))
			fatal_lang_error(parent::$name .'_no_pass_set', false);

		/* Lets md5 the pass */
		$pass = md5($tools->getSetting('pass'));

		/* Build the url */
		$url = $this->serverUrl. '?id='. $tools->getSetting('number_id') .'&md5pw='. $pass;

		/* Attempts to fetch data from an URL, regardless of PHP's allow_url_fopen setting */
		$data = $this->fetch_web_data($url);

		/* Oops, something went wrong, tell the user to try later */
		if ($data == null)
			fatal_lang_error(parent::$name .'_error_fetching_server', false);

		/* We got something */
		$data = explode(PHP_EOL, $data);

		/* Cleaning */
		foreach ($data as $key => $value)
			if (trim($value) == '')
				unset($data[$key]);

		/* The server says no */
		if ($data[0] == '-1')
			fatal_lang_error(parent::$name .'_error_from_server', false, array($data[2]));

		/* Make sure the data is what is supposed to be, $data[1] must match this regex: /\((.+)\)/ */
		if (preg_match('/\((.+)\)/', $data[1]))
		{
			/* Make a quick query to see if theres data already saved */
			$query = $this->_smcFunc['db_query']('', '
				SELECT customer_code
				FROM {db_prefix}'. parent::$_dbTableName .'',
				array()
			);

			while($row = $this->_smcFunc['db_fetch_assoc']($query))
				$result = $row;

			$this->_smcFunc['db_free_result']($query);

			/* The following data will be converted to an int */
			$data[0] = (int) $data[0];
			$data[2] = (int) $data[2];
			$data[5] = (int) $data[5];

			/* There is, so make an update */
			if (!empty($result))
			{
				/* Update the cache */
				$tools->killCache();

				$query = $this->_smcFunc['db_query']('', '
					UPDATE {db_prefix}'. parent::$_dbTableName .'
					SET edition_code = {int:edition_code},
						modules = {string:modules},
						remote_auth_capable = {int:remote_auth_capable},
						full_service = {string:full_service},
						expiration_date = {string:expiration_date},
						remote_auth_enable = {int:remote_auth_enable},
						remote_auth_url = {string:remote_auth_url},
						server_name = {string:server_name},
						tcp_port = {string:tcp_port},
						control_panel_login = {string:control_panel_login},
						chat_title = {string:chat_title},
						product_code = {string:product_code},
						customer_code = {string:customer_code}',
					array(
						'edition_code' =>$data[0],
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
			}

			/* No data, create the rows */
			else
				$this->_smcFunc['db_insert']('replace',
					'{db_prefix}'. parent::$_dbTableName,
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
}
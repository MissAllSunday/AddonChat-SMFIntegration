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

function wrapper_profile_page(){ AddonChat::profileDispatch(); }

class AddonChat
{
	protected $_user;
	protected $_data = array();
	protected $_rows = array();
	static protected $_dbTableName = 'addonchat';
	static protected $name = 'AddonChat';
	private $serverUrl = 'http://clientx.addonchat.com/queryaccount.php';

	public function __construct($user)
	{
	}

	protected function query()
	{
		return new AddonChatQuery(self::$_dbTableName);
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
		$query = $this->query();
		$tools = $this->tools();

		/* We need the password and the ID, lets check if we have it, if not tell the user to store it first */
		if (!$tools->enable('pass') || !$tools->enable('number_id'))
			fatal_lang_error(self::$name'_no_pass_set', false);

		/* Requires a function in a source file far far away... */
		require_once($sourcedir .'/Subs-Package.php');

		/* Build the url */
		$url = self::$serverUrl. '?id=', $tools->getSetting('number_id') ,'&md5pw= ', urlencode($tools->getSetting('pass'));

		/* Attempts to fetch data from a URL, regardless of PHP's allow_url_fopen setting */
		$data = fetch_web_data($url);

		/* Ups, something went wrong, tell the user to try later */
		if ($data == null)
			fatal_lang_error(self::$name'_error_fetching_server', false);

		/* We got something */
		$data = explode('\n', $data);

		/* Cleaning */
		foreach ($data as $key => $value)
			if (trim($value) == '')
				unset($data[$key]);

		/* The server says no */
		if ($data[0] == '-1')
			fatal_lang_error(self::$name'_error_from_server', false, $data[2]);

		/* Make sure the data is what is supposed to be, $data[0] must be an INT, $data[1] must match this regex: \(.+\) */
		if (is_int($data[0]) && preg_match('\(.+\)', $data[1]))
		{
			/* Make a quick query to see if theres data already saved */
			$query->params(
				'rows' => '*',
			);
			$query->getData(null, false);
			$result = $query->dataResult();

			/* There is, so make an update */
			if (!empty($result))
			{
				/* Update the cache */
				$this->killCache();

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

		/* Return the data */
		return $data;
	}
}
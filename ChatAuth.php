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

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

	global $user_info;

	/* We need both username and password */
	if (!isset($_REQUEST['username']) || !isset($_REQUEST['password']))
		$context[self::$name]['ras'] = '-1'. PHP_EOL;

	/* Do something here... */
	else
		$context[self::$name]['ras'] = 'user.uid  = '. $user_info['id'] . PHP_EOL .'user.usergroup.can_login  = true '. PHP_EOL .'user.usergroup.icon = 0'. PHP_EOL .'user.usergroup.can_msg = true'. PHP_EOL .'user.usergroup.idle_kick = true'. PHP_EOL .'';

	/* The external server needs a plain text file... */
	header("Content-type: text/plain");

	/* Print it */
	print $context[Addonchat::$name]['ras'];
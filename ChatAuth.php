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

	global $user_info, $modSettings;

	/* The mod must be enable */
	if (empty($modSettings['Addonchat_enable_general']))
		die('-1'. PHP_EOL);

	/* The external server needs a plain text file... */
	header('Content-type: text/plain');

	/* We need both username and password */
	if (!isset($_REQUEST['username']) || !isset($_REQUEST['password']) || empty($_REQUEST['username']) || empty($_REQUEST['password']))
		die('-1'. PHP_EOL);

	/* Cleaning */
	$_REQUEST['username'] = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['username']));
	$_REQUEST['password'] = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['password']));

	/* Check if this is the right user */
	if ($user_info['passwd'] != $_REQUEST['password'] || $user_info['username'] != $_REQUEST['username'])
		die('-1'. PHP_EOL);

	/* There will be a lot of checks here */

	/* Print it */
	print 'scras.version = 2.1'. PHP_EOL;
	print 'user.usergroup.id = 0'. PHP_EOL;
	print 'user.uid  = '. $user_info['id'] . PHP_EOL;
	print 'user.usergroup.can_login  = true'. PHP_EOL;
	print 'user.usergroup.icon = 0'. PHP_EOL;
	print 'user.usergroup.can_msg = true'. PHP_EOL;
	print 'user.usergroup.idle_kick = true'. PHP_EOL;

	/* Thats al we need */
	die();
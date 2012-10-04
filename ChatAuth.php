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

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

	global $memberContext, $sourcedir;

	$tools = AddonChat::tools();

	/* The mod must be enable */
	if (!$tools->enable('enable_general'))
		die('-1'. PHP_EOL);

	/* The external server needs a plain text file... */
	header('Content-type: text/plain');

	/* We need both username and password */
	if (!isset($_REQUEST['username']) || !isset($_REQUEST['password']) || empty($_REQUEST['username']) || empty($_REQUEST['password']))
		 die('-1'. PHP_EOL);

	/* Cleaning */
	$_REQUEST['username'] = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['username']));
	$_REQUEST['password'] = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['password']));

	/* Load the users data */
	$temp = loadMemberData($_REQUEST['username'], true, 'profile');

	/* We got something, SMF always returns the data as an array */
	if (!empty($temp) && is_array($temp))
	{
		loadMemberContext($temp[0]);
		$user = $memberContext[$temp[0]];

		/* Check if this is the right user */
		if (md5($user['registered_timestamp']) != $_REQUEST['password'] || $user['username'] != $_REQUEST['username'])
			die('-1'. PHP_EOL);

		/* Print it the general info*/
		print 'scras.version = 2.1'. PHP_EOL;
		print 'user.usergroup.id = 0'. PHP_EOL;
		print 'user.uid = '. $user['id'] . PHP_EOL;
		print 'user.usergroup.can_login  = 1'. PHP_EOL;
		print 'user.usergroup.idle_kick = 1'. PHP_EOL;

		/* Permissions and settings for admin only */
		print 'user.usergroup.is_admin = '. ($user['group_id'] == 1 ? '1' : '0') . PHP_EOL;
		print 'user.usergroup.allow_html = '. ($user['group_id'] == 1 ? '1' : '0') . PHP_EOL;

		/* Print the title */
		if (!empty($user['group']))
			print 'user.usergroup.title = "'. strip_tags($user['group']) .'"'. PHP_EOL;

		/* Set the icon */
		if (!empty($user['group_id']))
			switch ($user['group_id'])
			{
				case 1:
					print 'user.usergroup.icon = 2'. PHP_EOL;
					break;
				case 2:
					print 'user.usergroup.icon = 3'. PHP_EOL;
					break;
				case 0:
					print 'user.usergroup.icon = 0'. PHP_EOL;
					break;
				default:
					print 'user.usergroup.icon = 0'. PHP_EOL;
					break;
			}

		/* Print specific permissions by user */
		foreach (AddonChat::$permissions as $k)
		{
			if (allowedTo(AddonChat::$name .'_'. $k))
				print 'user.usergroup.'. $k .' = 1'. PHP_EOL;

			else
				print 'user.usergroup.'. $k .' = 0'. PHP_EOL;
		}

		/* Show the users avatar */
		$template = "<table border=0 cellpadding=0 cellspacing=3><tr><td valign=top align=left><img width='48' src='" . $sourcedir . "/ChatAvatar.php?u=\$uid' /></td><td align=left valign=top>\$time \$username:<br>\$message</td></tr></table>";

		print "chatpane.format.public.avatar = $template\n";
		print "chatpane.format.action.avatar = $template\n";
		print "chatpane.format.private.avatar = $template\n";
		print "chatpane.format.recompile = true\n";

		/* General settings */
		if ($tools->enable('max_msg_length'))
			print 'user.usergroup.max_msg_length = '. $tools->getSetting('max_msg_length') . PHP_EOL;
	}

	/* no? */
	else
	{
		print 'user.usergroup.can_login = 0'. PHP_EOL;
		print 'user.auth.msg = Invalid user'. PHP_EOL;
	}

	/* Thats all we need */
	die();
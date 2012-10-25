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

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

	global $memberContext, $boardurl;

	/* The external server needs a plain text file... */
	header('Content-type: text/plain');

	/* Call the tools */
	$tools = AddonChat::tools();

	/* The mod must be enable */
	if (!$tools->enable('enable_general'))
		die('-1'. PHP_EOL);

	/* We need both username and password */
	if (!isset($_REQUEST['username']) || !isset($_REQUEST['password']) || empty($_REQUEST['username']) || empty($_REQUEST['password']))
		die('-1'. PHP_EOL);

	/* Cleaning */
	$_REQUEST['username'] = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['username']));
	$_REQUEST['password'] = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['password']));

	/* You must be capable of using RAS and it must be enable */
	if (AddonChat::enableRAS() == true)
		die('-1'. PHP_EOL);

	/* Load the users data */
	$user = $tools->loadData($_REQUEST['username']);

	/* We got something, SMF always returns the data as an array */
	if (!empty($user) && is_array($user))
	{
		/* Check if this is the right user */
		if (md5($user['passwd']) != $_REQUEST['password'] || $user['member_name'] != $_REQUEST['username'])
			die('-1'. PHP_EOL);

		/* Print it the general info*/
		print 'scras.version = 2.1'. PHP_EOL;
		print 'user.usergroup.id = 0'. PHP_EOL;
		print 'user.uid = '. $user['id_member'] . PHP_EOL;
		print 'user.usergroup.can_login  = 1'. PHP_EOL;
		print 'user.usergroup.idle_kick = 1'. PHP_EOL;

		/* Permissions and settings for admin only */
		print 'user.usergroup.is_admin = '. ($user['id_group'] == 1 ? '1' : '0') . PHP_EOL;
		print 'user.usergroup.allow_html = '. ($user['id_group'] == 1 ? '1' : '0') . PHP_EOL;

		/* Print the title */
		if (!empty($user['member_group']))
			print 'user.usergroup.title = "'. $user['member_group'] .'"'. PHP_EOL;

		/* Set the icon */
		if (!empty($user['id_group']))
			switch ($user['id_group'])
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

		else
			print 'user.usergroup.icon = 0'. PHP_EOL;

		/* Load the permissions */
		$permissions = $tools->loadPermissions($user['additional_groups']);

		/* Print specific permissions by user */
		foreach (AddonChat::$permissions as $k)
		{
			/* Admins can  do everything */
			if (!empty($user['id_group']) && $user['id_group'] == 1)
				print 'user.usergroup.'. $k .' = 1'. PHP_EOL;

			/* Lets check regular users */
			else
			{
				if (in_array(AddonChat::$name .'_'. $k, $permissions))
					print 'user.usergroup.'. $k .' = 1'. PHP_EOL;

				else
					print 'user.usergroup.'. $k .' = 0'. PHP_EOL;
			}
		}

		/* Show the users avatar */
		if ($tools->enable('allow_avatar'))
		{
			$template = "<table border=0 cellpadding=0 cellspacing=3><tr><td valign=top align=left><img width='48' src='" . $boardurl . "/ChatAvatar.php?uid=\$uid' /></td><td align=left valign=top>\$time \$username:<br>\$message</td></tr></table>";

			print "chatpane.format.public.avatar = $template\n";
			print "chatpane.format.action.avatar = $template\n";
			print "chatpane.format.private.avatar = $template\n";
			print "chatpane.format.recompile = true\n";
		}

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
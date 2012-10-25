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

	global $memberContext, $sourcedir, $smcFunc, $settings;

	/* Call the tools */
	$tools = AddonChat::tools();

	/* The mod must be enable */
	if (!$tools->enable('enable_general'))
	{
		header('Location: '. $settings['default_theme_url']. '/images/chat_default_avatar.gif');
		exit(0);
	}

	/* We need the username */
	if (!isset($_REQUEST['uid']) || empty($_REQUEST['uid']))
	{
		header('Location: '. $settings['default_theme_url']. '/images/chat_default_avatar.gif');
		exit(0);
	}

	/* Cleaning */
	$_REQUEST['uid'] = (int) $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['uid']));

	/* Load the users data */
	$temp = loadMemberData($_REQUEST['uid'], false, 'profile');
			loadMemberContext($_REQUEST['uid']);

	$user = $memberContext[$_REQUEST['uid']];

	/* Show the avatar */
	if (!empty($user['avatar']))
	{
		header('Location: '. $user['avatar']['href']);
		exit(0);
	}

	/* We need to provide a default avatar */
	else
	{
		header('Location: '. $settings['default_theme_url']. '/images/chat_default_avatar.gif');
		exit(0);
	}
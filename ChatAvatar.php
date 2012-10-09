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

	global $memberContext, $sourcedir, $smcFunc;

	/* Call the tools */
	$tools = AddonChat::tools();

	/* The mod must be enable */
	if (!$tools->enable('enable_general'))
		die('-1'. PHP_EOL);

	/* We need the username */
	if (!isset($_REQUEST['uid']) || empty($_REQUEST['uid']))
		die('-1'. PHP_EOL);

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

	else
	{
		header('Location: http://missallsunday.com/avatars/Actors/Cameron_Diaz.jpg');
		exit(0);
	}
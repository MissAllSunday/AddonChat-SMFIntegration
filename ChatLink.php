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

	global $smcFunc;

	/* Cleaning */
	$user = (int) $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['uid']));

	/* Set the link */
	$url = "index.php?action=profile;u=" . $user;
	header("location: $url");
	exit;
<?php

/**
 * AddonChat Integration mod (SMF)
 *
 * @package SMF
 * @author Suki <missallsunday@simplemachines.org>
 * @copyright 2012 Jessica Gonz�lez
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

global $scripturl, $txt;

$txt['AddonChat_title_main'] = 'Chat';
$txt['AddonChat_default_menu'] = 'Chat Integration';
$txt['AddonChat_general_settings'] = 'Settings';
$txt['AddonChat_look_settings'] = 'Look settings';
$txt['AddonChat_admin_panel_desc'] = 'From here you can configure your AddonChat integration.<br />After you have saved your ID and password, please click on the link below to connect your forum with the chat server<br /> You only need to do this once, after this, the settings will be stored on the DB.<br />';
$txt['AddonChat_enable_general'] = 'Enable the addonChat integration';
$txt['AddonChat_enable_general_sub'] = 'This is the master setting, needs to be on for the mod to work properly.';
$txt['AddonChat_number_id'] = 'Your number ID';
$txt['AddonChat_number_id_sub'] = 'Numeric portion of AddonChat account number. Should be something like: SC-000, take away the SC- part and just use the numbers.';
$txt['AddonChat_pass'] = 'Your addonChat password';
$txt['AddonChat_pass_sub'] = 'The integration script needs your password to connect with the chat server.';
$txt['AddonChat_server_call'] = '<a href="'. $scripturl . '?action=admin;area='. AddonChat::$name .';sa=general;server">Server Call</a>';
$txt['AddonChat_no_pass_set'] = 'You need to introduce your number ID and password first, please go back and fill out the required fields and try again.';
$txt['AddonChat_error_from_server'] = 'There is an error comming from the server, the error is: %s';
$txt['AddonChat_error_fetching_server'] = 'The server isn\'t responding, please try again later';
$txt['AddonChat_server_OK'] = 'The settings were saved succesfully';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';


// Who's online strings
$txt['whoall_chat'] = 'Viewing the <a href="'. $scripturl. '?action=faq">Chat page</a>.';
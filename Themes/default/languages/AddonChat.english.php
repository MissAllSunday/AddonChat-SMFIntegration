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
$txt['AddonChat_settings_message_true'] = '<strong>You have successfully connected to the chat server</strong>, the info has been added to the DB.<p /> Follow the steps below to enable remote authentication for your AddonChat chat room:<br />
<ol>
<li><a href="%1$s" target="_blank">Login to your AddonChat Customer Account</a></li>
<li>Enter the AddonChat Account Control Panel for the account that you wish to enable remote authentication</li>
<li>Select the Settings tab from within the AdodnChat Account Control Panel</li>
<li>Select the Site Integration link in the Settings submenu</li>
<li>Select the Remote Authentication link in the Site Integration sub-menu</li>
<li>Set Enable Remote Authentication to Yes</li>
<li>Enter the full URL (beginning with http://) to your authentication script next to Authentication URL, your unique full url is: <strong>%2$s</strong></li>
<li>Click the "Click Here to Save Changes" button</li>
</ol><p />If you wish to update the settings, you can call the server again by clicking this link: '. $txt['AddonChat_server_call'];
$txt['AddonChat_settings_message_false'] = 'You haven\'t connect to the server yet, you need to connect to the server to be able to use this mod, please add your ID and password in the fields below and click save.<p />After that please click this link: '. $txt['AddonChat_server_call'] .' to connect to the chat server, if sucesfully, you will see the next steps.';
$txt['AddonChat_menu_position'] = 'Select the position for the FAQ button in the menu';
$txt['AddonChat_menu_position_sub'] = 'By default is next to home.';
$txt['AddonChat_menu_home'] = 'Next to the Home button';
$txt['AddonChat_menu_help'] = 'Next to the Help button';
$txt['AddonChat_menu_search'] = 'Next to the Search button';
$txt['AddonChat_menu_login'] = 'Next to the Login button';
$txt['AddonChat_menu_register'] = 'Next to the Register button';
$txt['AddonChat_noscript'] = 'To enter this chat room, please enable JavaScript in your web
								browser. This <a href="http://www.addonchat.com/">Chat
								Software</a> requires Java: <a href="http://www.java.com/">Get
								Java Now</a>';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';
$txt['AddonChat_'] = '';


// Who's online strings
$txt['whoall_chat'] = 'Viewing the <a href="'. $scripturl. '?action=faq">Chat page</a>.';
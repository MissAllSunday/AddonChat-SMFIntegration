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
$txt['AddonChat_permission_style'] = 'Select how do you want to hadle the chat permissions';
$txt['AddonChat_permission_style_sub'] = 'by group will group all similar permissions under 3 big permissions, admin, moderator and regular user<br /> individual will create individual SMF permissions foreach chat permission, keep in mind that if you change how the permissions are used, you need to properly set all the permissions for your user groups all over again (this include profile permissions), it is highly recommended that you don\'t change this setting that often.';
$txt['AddonChat_permission_style_individual'] = 'Individual';
$txt['AddonChat_permission_style_group'] = 'Group';
$txt['AddonChat_max_msg_length'] = 'Maximum message length in characters';
$txt['AddonChat_max_msg_length_sub'] = 'This counts spaces as well.';
$txt['AddonChat_no_guest'] = 'I\'m sorry, guest cannot see the chat';

/* Permissions strings */
$txt['permissiongroup_simple_AddonChat_per_simple'] = 'AddonChat mod permissions';
$txt['permissiongroup_AddonChat_per_classic'] = 'AddonChat mod permissions';
$txt['permissionname_AddonChat_can_msg'] = 'Allow the user to post messages on the chat';
$txt['permissionname_AddonChat_can_action'] = 'Allow the user to send action messages';
$txt['permissionname_AddonChat_allow_pm'] = 'Allow the user to send private messages on the chat';
$txt['permissionname_AddonChat_allow_room_create'] = 'allow the user to create new rooms';
$txt['permissionname_AddonChat_allow_avatars'] = 'Allow the user to select an avatar';
$txt['permissionname_AddonChat_can_random'] = 'Allow the user to use the /roll command';
$txt['permissionname_AddonChat_allow_bbcode'] = 'Allow BBCode in messages';
$txt['permissionname_AddonChat_allow_color'] = 'Allow user to set message color';
$txt['permissionname_AddonChat_msg_scroll'] = 'Enable message scroll-back feature';
$txt['permissionname_AddonChat_filter_shout'] = 'Apply shout filter to this user';
$txt['permissionname_AddonChat_filter_profanity'] = 'Apply word filters to this user';
$txt['permissionname_AddonChat_filter_word_replace'] = 'Apply text replacement filters to this user';
$txt['permissionname_AddonChat_can_nick'] = 'Allow user to change his/her name using /nick command';
$txt['permissionname_AddonChat_can_kick'] = 'Allow user to kick other users';
$txt['permissionname_AddonChat_can_affect_admin'] = 'Allow user to affect administrators';
$txt['permissionname_AddonChat_can_grant'] = 'Can this user grant administrative privileges';
$txt['permissionname_AddonChat_can_cloak'] = 'Can this user cloak';
$txt['permissionname_AddonChat_can_see_cloak'] = 'Can this user see cloaked users';
$txt['permissionname_AddonChat_login_cloaked'] = 'Forces user to be logged in cloaked';
$txt['permissionname_AddonChat_can_ban'] = 'Can this user ban IP addresses';
$txt['permissionname_AddonChat_can_ban_subnet'] = 'Can this user ban Class C subnets';
$txt['permissionname_AddonChat_can_system_speak'] = 'Can this user speak as the system user';
$txt['permissionname_AddonChat_can_silence'] = 'Can this user silence others';
$txt['permissionname_AddonChat_can_fnick'] = 'Allow user use the /fnick command';
$txt['permissionname_AddonChat_can_launch_website'] = 'Allow user to launch websites for other users';
$txt['permissionname_AddonChat_can_transfer'] = 'Allow user to transfer users to another room';
$txt['permissionname_AddonChat_can_join_nopw'] = 'Allow user to join password protected rooms freely';
$txt['permissionname_AddonChat_can_topic'] = 'Allow user to set room topics';
$txt['permissionname_AddonChat_can_close'] = 'Allow user to close rooms';
$txt['permissionname_AddonChat_can_ipquery'] = 'Allow user to query IP addresses of other users';
$txt['permissionname_AddonChat_can_geo_locate'] = 'Allow user to query geographic location of other users';
$txt['permissionname_AddonChat_can_query_ether'] = 'Allow user to query ether';
$txt['permissionname_AddonChat_can_clear_screen'] = 'Allow user to clear screens of other users';
$txt['permissionname_AddonChat_can_clear_history'] = 'Allow user to clear recent room history';
$txt['permissionname_AddonChat_allow_room_create'] = 'Allow user to create new rooms';
$txt['permissionname_AddonChat_see_chat'] = 'Allow the user to see the Chat';
$txt['cannot_AddonChat_see_chat'] = 'I\'m sorry, you are not allowed to view the Chat.';

// Who's online strings
$txt['whoall_chat'] = 'Viewing the <a href="'. $scripturl. '?action=chat">Chat page</a>.';
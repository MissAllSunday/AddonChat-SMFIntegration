<?php

/**
 * AddonChat Integration mod (SMF)
 *
 * @package Addonchat Integration
 * @author Suki <missallsunday@simplemachines.org>
 * @copyright 2012 Jessica Gonz�lez
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

	function template_addonChat_main()
	{
		global $context, $scripturl, $modSettings, $user_info;

		/* If there is any issue, print it, but only for admins */
		if (!empty($context[AddonChat::$name]['issue']) && $context['user']['is_admin'])
			echo '<div class="information">'. $context[AddonChat::$name]['issue'] .'</div>';

		/* There are issues but you aren't an admin, you don't get any goodies... */
		elseif (!empty($context[AddonChat::$name]['issue']) && !$context['user']['is_admin'])
			echo '<div class="information">'. $context[AddonChat::$name]['tools']->getText('issues_guest') .'</div>';

		/* We are good to go */
		else
			echo '
			<div class="cat_bar">
				<h3 class="catbg">
					<span class="ie6_header floatleft">
						', $context[AddonChat::$name]['tools']->getText('title_main') ,'
					</span>
				</h3>
			</div>
			<span class="clear upperframe">
				<span></span>
			</span>
			<div class="roundframe rfix">
				<div class="innerframe">
					<div class="content" style="margin:auto; text-align:center;">
						<script type="text/javascript">/*<![CDATA[*/
							var addonchat = {
								signed:true,
								server:', $context[AddonChat::$name]['global_settings']['server_id'] ,',
								id:', $context[AddonChat::$name]['tools']->getSetting('number_id') ,',
								width:"625",
								height:"380",
								language:"en"
							}
							var addonchat_param = {
								username: "'. $user_info['name'] .'",
								password: "'. md5($user_info['passwd']) .'",
								autologin: true,
								url_exit_enable: true,
								url_exit: "'. $scripturl .'"
							}
							 /* ]]> */
						</script>
						<script type="text/javascript"
								src="http://'. $context[AddonChat::$name]['tools']->globalSetting('server_name') .'/chat.js"></script>
						<noscript>
							', $context[AddonChat::$name]['tools']->getText('noscript') ,'
						</noscript>
					</div>
				</div>
			</div>
			<span class="lowerframe">
				<span></span>
			</span>';
	}
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

	function template_addonChat_main()
	{
		global $context, $scripturl, $modSettings, $user_info;

		echo '
			<div class="cat_bar">
				<h3 class="catbg">
					<span class="ie6_header floatleft">
						some title here
					</span>
				</h3>
			</div>
			<span class="clear upperframe">
				<span></span>
			</span>
			<div class="roundframe rfix">
				<div class="innerframe">
					<div class="content">
						<script type="text/javascript">/*<![CDATA[*/
							var addonchat = {
								signed:true,
								server:1,
								id:', $context[AddonChat::$_name]['tools']->getSetting('number_id') ,',
								width:"625",
								height:"380",
								language:"en"
							}
							var addonchat_param = {
								username: '. $user_info['username'] .',
								password: '. $user_info['passwd'] .',
								autologin: true,
								mycolor: "#000000",
								myfont: "Verdana-PLAIN-13",
								url_exit_enable: true,
								url_exit: "http://www.addonchat.com"
							}
							 /* ]]> */</script>
							 <script type="text/javascript"
								src="http://client1.addonchat.com/chat.js"></script>
							 <noscript>
								To enter this chat room, please enable JavaScript in your web
								browser. This <a href="http://www.addonchat.com/">Chat
								Software</a> requires Java: <a href="http://www.java.com/">Get
								Java Now</a>
							</noscript>
					</div>
				</div>
			</div>
			<span class="lowerframe">
				<span></span>
			</span><br />';
	}

	function template_addonChat_ras()
	{
		global $context;

		print $context[AddonChat::$_name]['ras'];
	}
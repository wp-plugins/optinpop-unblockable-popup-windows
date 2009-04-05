<?php
/*
Plugin Name: OptinPop
Plugin URI: http://BigSellingOptins.com/Plugin
Description: Easily create unblockable popup windows that can display the content of any web page you choose. You can tune the appearance of your pop-up window and other parameters on the OptinPop Settings page within the Plugins menu. Created by <a href="http://BigSellingOptins.com/Plugin">BigSellingOptins.com</a> and <a href="http://iCoder.com">iCoder.com</a>.
Version: 2.1
Author: Brian Terry, Michel Komarov
Author URI: http://BigSellingOptins.com

OptinPop Plug-in is released under
Artistic License 2.0
Copyright (c) 2008, ReactorPublishing.com and iCoder.com 

This is the Standard Version Package.

You are permitted to use the Standard Version and create and
use Modified Versions for any purpose without restriction,
provided that you do not Distribute the Modified Version.

You may Distribute verbatim copies of the Source form of
the Standard Version of this Package in any medium without
restriction.

Refer to license.txt for more details.

 * This software uses GreyBox library under GNU LGPL.
 * This software is a "work that uses the Library"
 * in terms of LGPL and is staying outside the scope of LGPL.
 * You can copy the GreyBox from http://orangoo.com/labs/GreyBox/
 * (Read the readme.txt in ./greybox folder for more details)
 */

define ('iOptinPop_RSSFEEDS', 
<<<RSS_FEEDS
http://askmichel.icoder.com/feed/
http://www.edrivis.com/?feed=rss2
http://feeds.copyblogger.com/Copyblogger
http://feeds.feedburner.com/TheMichelFortinBlog
http://feeds.feedburner.com/BigSellingWebsiteDesignBlog
http://feeds.feedburner.com/TerryDean
RSS_FEEDS
);

define('OPTINPOP_MENU_PAGE', 'optinpop-config');
$iOptinPop_url = '';

function iOptinPop_init() {
	global $iOptinPop_url;
	$iOptinPop_url = trim(get_option('iOptinPop_url'));
	add_action('admin_menu', 'iOptinPop_config_page');
}
add_action('init', 'iOptinPop_init');

function iOptinPop_config_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('OptinPop Settings'), __('OptinPop Settings')
			, 'manage_options', OPTINPOP_MENU_PAGE, 'iOptinPop_conf');
}

function iOptinPop_conf() {
	global $iOptinPop_url;

	if ( isset($_POST['submit']) ) {

		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));

		if (isset($_POST['OptinPop_url']) && trim($_POST['OptinPop_url'])) {
			update_option('iOptinPop_url', $iOptinPop_url = trim($_POST['OptinPop_url']));
		} else {
			delete_option('iOptinPop_url');
		}

		if (isset($_POST['OptinPop_width']) && intval($_POST['OptinPop_width']) > 0) {
			update_option('iOptinPop_box_width', intval($_POST['OptinPop_width']));
		} else {
			delete_option('iOptinPop_box_width');
		}

		if (isset($_POST['OptinPop_height']) && intval($_POST['OptinPop_height']) > 0) {
			update_option('iOptinPop_box_height', intval($_POST['OptinPop_height']));
		} else {
			delete_option('iOptinPop_box_height');
		}

		if (isset($_POST['OptinPop_centered']) && intval($_POST['OptinPop_centered']) > 0) {
			update_option('iOptinPop_box_centered', intval($_POST['OptinPop_centered']));
		} else {
			delete_option('iOptinPop_box_centered');
		}

		if (isset($_POST['OptinPop_lightbox']) && trim($_POST['OptinPop_lightbox'])) {
			update_option('iOptinPop_box_lightbox', trim($_POST['OptinPop_lightbox']));
		} else {
			delete_option('iOptinPop_box_lightbox');
		}

		if (isset($_POST['OptinPop_show']) && trim($_POST['OptinPop_show'])) {
			update_option('iOptinPop_box_show', trim($_POST['OptinPop_show']));
		} else {
			delete_option('iOptinPop_box_show');
		}

		if (isset($_POST['OptinPop_delay']) && intval($_POST['OptinPop_delay']) > 0
		&& isset($_POST['OptinPop_show']) && 'exit' != trim($_POST['OptinPop_show'])) {
			update_option('iOptinPop_box_delay', intval($_POST['OptinPop_delay']));
		} else {
			delete_option('iOptinPop_box_delay');
		}

		if (isset($_POST['OptinPop_time']) && intval($_POST['OptinPop_time']) > 0
		&& isset($_POST['OptinPop_time_days']) && intval($_POST['OptinPop_time_days']) > 0) {
			update_option('iOptinPop_box_time', intval($_POST['OptinPop_time_days']));
		} else {
			delete_option('iOptinPop_box_time');
		}
	} // if ( isset($_POST['submit']) )

	if ( !empty($_POST ) ) {
		echo '<div id="message" class="updated fade"><p><strong>'
		. __('Options saved.') . '</strong></p></div>';
	}

	echo '<div class="wrap"><h2>' . __('OptinPop Settings') . '</h2>';
	echo html_OptinPopConf_Styles();
	echo html_OptinPopConf_RegisterForm('Step 1: Register');
	echo html_OptinPopConf_WindowDetails('Step 2: Configure');
	print_html_OptinPopConf_BlogList('Step 3: Profit from your blog');
	echo html_OptinPopConf_Footer();
	echo '</div>';

} // function iOptinPop_conf()

if ( !trim(get_option('iOptinPop_url')) && !isset($_POST['submit']) ) {
	function iOptinPop_warning() {
		echo "
		<div id='iOptinPop-warning' class='updated fade'><p><strong>"
		.__('The OptinPop plugin is not active yet.')."</strong> "
		.sprintf(
			__('You\'ll need to <a href="%1$s">enter your pop-up page URL</a> for it to work.')
			, "plugins.php?page=".OPTINPOP_MENU_PAGE)
		."</p></div>
		";
	}
	add_action('admin_notices', 'iOptinPop_warning');
	return;
}

add_action('wp_head', 'iOptinPop_box');
function iOptinPop_box() {
	global $iOptinPop_url;

	$box_showing = 86400 * intval(get_option('iOptinPop_box_time'));
	$box_width = intval(get_option('iOptinPop_box_width'));
	$box_height = intval(get_option('iOptinPop_box_height'));
	$box_id = 'iOptinPopLink'.time();

	if (trim($iOptinPop_url)
	&& (!isset($_COOKIE['iOptinPopBox'])
	|| time() - intval($_COOKIE['iOptinPopBox']) > $box_showing)) {

		$gb_url = get_bloginfo('wpurl') . '/wp-content/plugins/optinpop/greybox/';
		echo "\n".'<script type="text/javascript">'
		. "\n".'document.cookie="iOptinPopBox='.time().'; path='.SITECOOKIEPATH
			.'; expires='.date('D, d M Y H:i:s',time()+max($box_showing,31536000)).' GMT; ";'
		. "\n".'GB_ROOT_DIR = "'.$gb_url.'";'
		. "\n".'</script>'
		. "\n".'<script src="'.$gb_url.'AJS.js" type="text/javascript"></script>'
		. "\n".'<script src="'.$gb_url.'AJS_fx.js" type="text/javascript"></script>'
		. "\n".'<script src="'.$gb_url.'gb_scripts.js" type="text/javascript"></script>'
		. "\n".'<link href="'.$gb_url.'gb_styles.css" rel="stylesheet" type="text/css"></script>'
		. "\n<script type=\"text/javascript\">\n<!--//"
		. "\n".'GB_show = function(caption, url, height, width, callback_fn) {'
		. "\n".'var options = {'
		. "\n".'caption: caption,'
		. "\n".'height: height || 500,'
		. "\n".'width: width || 500,'
		. (intval(get_option('iOptinPop_box_centered')) > 0? "\n".'center_win: true,': '')
		. "\n".'fullscreen: false,'
		. "\n".'callback_fn: callback_fn'
		. "\n".'}'
		. "\n".'var win = new GB_Window(options);'
		. "\n".'win.show(url);'
		. (intval(get_option('iOptinPop_box_lightbox')) > 0? '': "\n".'win.overlay.style.display="none";')
		. "\n".'}';

		if ('exit' == get_option('iOptinPop_box_show')) {
			echo "\nvar iOptinPopBoxStarted = 0;"
			. "\nvar iOptinPopBoxDelay = 0;"
			. "\nAJS.AEV(document,'mousemove',iOptinPopGetMouseY);"
			. "\nfunction iOptinPopGetMouseY(e) {"
			. "\ntempY = document.all? event.clientY: e.pageY - document.body.scrollTop;"
			. "\nif (tempY < 0) tempY = 0;"
			. "\nif (20 > tempY && 0 > --iOptinPopBoxDelay) {"
			. "\nif (0 == iOptinPopBoxStarted) {"
			. "\niOptinPopBoxStarted = 1;"
			. "\nGB_show('','$iOptinPop_url',$box_height,$box_width);"
			. "\n}}}";
		}
		else {
			echo "\nfunction iOptinPopDelay() {"
			. "\nsetTimeout(\"GB_show('','$iOptinPop_url',$box_height,$box_width);\","
				.(1000 * intval(get_option('iOptinPop_box_delay'))).");"
			. "\n}"
			. "\nAJS.AEV(window,'load',iOptinPopDelay);";
		}

		print "\n//-->\n</script>\n";
	} // if (trim($iOptinPop_url)... && SHOW
} // function iOptinPop_box()

function html_OptinPopConf_Styles() {
return <<<STYLES
<style type="text/css">
h3.dbx-handle {
	height: 19px;
	padding: 4px 1em 1px 0;
	font-size: 14px;
	color: navy;
}
div.dbx-title {
	font-family: Arial, sans serif;
	font-size: 13px;
	color: black;
}
div.dbx-head {
	font-family: Arial, sans serif;
	font-weight: bold;
	padding-bottom: 1ex;
	font-size: 17px;
	color: gray;
}

.submit {
	border:none;
	border-bottom: 1px solid silver;
	margin:0;
	padding:1.5em 0 1em 0;
}
.form-table td {
	padding: 8px;
	font-size: 11px;
	border-bottom: none;
}
#window-details td {
	color: #2583ad;
	font-size: 13px;
	font-weight: bold;
}
td.w50 {
	width: 50px;
}
.form-table th {
	text-align: right;
	width: 200px;
	color: #2583ad;
	border-bottom: none;
}
.form-table td label, .form-table th label {
	font-weight: normal;
}
</style>
STYLES;
} // function html_OptinPopConf_Styles()

function html_OptinPopConf_RegisterForm($header) {
return <<<REGISTER_FORM
<h3 class="dbx-handle">$header</h3>
<div class="dbx-title dbx-head">Register your plug-in (optional)</div>
<div class="dbx-title">Get free updates and a selection of 7 free popin window template design when you register.</div>
&nbsp;
<form method="post" action="http://www.aweber.com/scripts/addlead.pl"
target="external"><table class="form-table"><tbody>
<tr class="form-field"><th><label for="ar_name">Name</label></th>
	<td width="200"><input type="text" name="name" id="ar_name" size="25" /></td>
	<td>&nbsp;</td></tr>
<tr class="form-field"><th><label for="ar_email">Email</label></th>
	<td><input type="text" name="from" id="ar_email" size="25" /></td>
	<td style="font: normal 11px Arial; color:gray;" noWrap="noWrap">
		We never rent, sell or abuse your email in any way.<br />
		We respect your privacy and you can unsubscribe at anytime.
	</td></tr>
</tbody></table>
<p class="submit">
	<input type="hidden" name="unit" value="bigsellingoptin">
	<input type="hidden" name="meta_web_form_id" value="1171735743">
	<input type="hidden" name="meta_split_id" value="">
	<input type="hidden" name="meta_redirect_onlist" value="">
	<input type="hidden" name="meta_adtracking" value="optinpopplugin">
	<input type="hidden" name="meta_message" value="1">
	<input type="hidden" name="meta_required" value="from">
	<input type="hidden" name="meta_forward_vars" value="0">
	<input type="hidden" name="redirect" value="http://www.bigsellingoptins.com/confirm/">
	<input type="submit" name="submit" value="Register today" />
</p>
</form>
REGISTER_FORM;
} // function html_OptinPopConf_RegisterForm($header)

function html_OptinPopConf_WindowDetails($header) {
	$iOptinPop_url        = get_option('iOptinPop_url');
	$iOptinPop_box_width  = get_option('iOptinPop_box_width');
	$iOptinPop_box_height = get_option('iOptinPop_box_height');
	$iOptinPop_box_delay  = get_option('iOptinPop_box_delay');
	$iOptinPop_box_time   = get_option('iOptinPop_box_time');
	$iOptinPop_box_show_exit  = 'exit' == get_option('iOptinPop_box_show')? 'checked': '';
	$iOptinPop_box_show_load  = 'exit' != get_option('iOptinPop_box_show')? 'checked': '';
	$iOptinPop_box_centered_0 = intval(get_option('iOptinPop_box_centered')) > 0? '': 'checked';
	$iOptinPop_box_centered_1 = intval(get_option('iOptinPop_box_centered')) > 0? 'checked': '';
	$iOptinPop_box_lightbox_0 = intval(get_option('iOptinPop_box_lightbox')) > 0? '': 'checked';
	$iOptinPop_box_lightbox_1 = intval(get_option('iOptinPop_box_lightbox')) > 0? 'checked': '';
	$iOptinPop_box_time_0     = 0 == intval(get_option('iOptinPop_box_time'))? 'checked': '';
	$iOptinPop_box_time_1     = intval(get_option('iOptinPop_box_time')) > 0 ? 'checked': '';
return <<<WINDOW_DETAILS
<h3 class="dbx-handle">$header</h3>
<div class="dbx-title dbx-head">Configure your OptinPop window</div>
&nbsp;
<form action="" method="post" id="iOptinPop-conf"><table id="window-details" class="form-table"><tbody>
<tr class="form-field"><th><label for="OptinPop_url">URL of the page to show</label></th>
	<td colspan="2"><input id="OptinPop_url" name="OptinPop_url" type="text" size="50" value="$iOptinPop_url" /></td></tr>
<tr class="form-field"><th><label for="OptinPop_width">OptinPop Width</label></th>
	<td class="w50"><input id="OptinPop_width" type="text" size="3"
		name="OptinPop_width" value="$iOptinPop_box_width" /></td>
	<td><input type="radio" id="OptinPop_show_exit" style="width:auto;"
		name="OptinPop_show" value="exit" $iOptinPop_box_show_exit />
		<label for="OptinPop_show_exit">Show on Exit</label></td></tr>
<tr class="form-field"><th><label for="OptinPop_height">OptinPop Height</label></th>
	<td class="w50"><input id="OptinPop_height" type="text" size="3"
		name="OptinPop_height" value="$iOptinPop_box_height" /></td>
	<td><input type="radio" id="OptinPop_show_load" style="width:auto;"
		name="OptinPop_show" value="load" $iOptinPop_box_show_load />
		<label for="OptinPop_show_load" style="padding-right:2em;">Show on Load</label>
		<input id="OptinPop_delay" type="text" size="3" style="width:3em;"
		name="OptinPop_delay" value="$iOptinPop_box_delay" />
		<label for="OptinPop_delay">Seconds delay</label></td></tr>
<tr class="form-field"><th>OptinPop Position</th>
	<td class="w50"><input type="radio" id="OptinPop_position_top" style="width:auto;"
		name="OptinPop_centered" value="0" $iOptinPop_box_centered_0 />
		<label for="OptinPop_position_top">Top</label></td>
	<td><input type="radio" id="OptinPop_position_center" style="width:auto;"
		name="OptinPop_centered" value="1" $iOptinPop_box_centered_1 />
		<label for="OptinPop_position_center">Center</label></td></tr>
<tr class="form-field"><th>Lightbox Effect</th>
	<td class="w50"><input type="radio" id="OptinPop_lightbox_on" style="width:auto;"
		name="OptinPop_lightbox" value="1" $iOptinPop_box_lightbox_1 />
		<label for="OptinPop_lightbox_on">On</label></td>
	<td><input type="radio" id="OptinPop_lightbox_off" style="width:auto;"
		name="OptinPop_lightbox" value="0" $iOptinPop_box_lightbox_0 />
		<label for="OptinPop_lightbox_off">Off</label></td></tr>
<tr class="form-field"><th>&nbsp;</th><td colspan="2" style="padding-top:1em;">Show to a visitor</td></tr>
<tr class="form-field"><th><label for="OptinPop_time_always">Always</label></th>
	<td colspan="2"><input type="radio" id="OptinPop_time_always" style="width:auto;"
		name="OptinPop_time" value="0" $iOptinPop_box_time_0 />
		<label for="OptinPop_time_once" style="padding-left:2em;">Once in</label>
		<input type="radio" id="OptinPop_time_once" style="width:auto;"
		name="OptinPop_time" value="1" $iOptinPop_box_time_1 />
		<input type="text" id="OptinPop_time_days" size="3" style="width:3em;"
		name="OptinPop_time_days" value="$iOptinPop_box_time" />
		<label for="OptinPop_time_days">Days<label></td></tr>
</tbody></table>
<p class="submit">
	<input type="submit" name="submit" value="Update options &raquo;" />
</p>
</form>
WINDOW_DETAILS;
} // function html_OptinPopConf_WindowDetails($header)

function print_html_OptinPopConf_BlogList($header) {
echo "
<style type='text/css'>
div.dashboard-widget-content {
    padding: 10px 15px;
}
div.dashboard-widget-content ul {
    margin: 0;
    text-indent: 0;
    padding-left: 15px;
}
div.dashboard-widget-content li {
    margin: .5em 0 1em;
}
div.dashboard-widget-content ul {
    list-style: none;
    padding: 0;
}
div.dashboard-widget-content ul li {
    display: block;
    width: 19.95%;
    padding-bottom: 10px;
    margin: 0;
    float: left;
    font-size: 12px;
}
div.dashboard-widget-content {
    margin: 10px 5px;
    padding: 0;
}

div.dashboard-widget-content ul li .post {
    display:block;
    font-family:Georgia,'Times New Roman',Times,serif;
    font-size:17px;
    line-height: 1.2em;
    height:90px;
    overflow:hidden;
}

div.dashboard-widget-content ul li a {
    display: block;
    height:100%;
    overflow:hidden;
    margin: 5px 10px;
    text-decoration: none;
    padding: .5em;
    border-right: 1px solid #dadada;
    border-bottom: 1px solid #dadada;
	background-color: #f9f9f9;
}
div.dashboard-widget-content ul li a cite {
    display: block;
    font-family: 'Lucida Sans', 'Lucida Grande', 'Lucida Sans Unicode', Tahoma, Verdana, sans-serif;
    height:28px;
}
</style>";
	echo "
		<h3 class='dbx-handle'>$header</h3>
		<div class='dbx-title dbx-head'>Turn your bright ideas into big selling blogs</div>
		<div class='dbx-title'>Visit the blog posts below
		for the latest blog marketing tips, techniques and strategies...</div>&nbsp;
		<div class='dashboard-widget-content'>
	";
	if (count($RSSfeeds = array_filter(explode("\n", iOptinPop_RSSFEEDS), 'trim')) > 0) {
		echo "\n<ul id='planetnews'>\n";
		shuffle($RSSfeeds);
		$rssLinks = array();
		$rssItems = array();
		$rssNum = 0;
		require_once (ABSPATH . WPINC . '/rss.php');
		foreach ($RSSfeeds as $rssURL) {
			if (is_object($rss = @fetch_rss(trim($rssURL)))
			&& isset($rss->channel) && isset($rss->items)
			&& is_array($rss->channel) && is_array($rss->items)) {
				$rssLinks[] = array(
					'blog' => ($blog = $rss->channel['title']),
					'link' => $rss->channel['link']
				);
				$rssItems[$rssNum] = array();
				foreach ($rss->items as $item ) {
					$rssItems[$rssNum][]
						= '<li><a href="' . wp_filter_kses($item['link']) . '"><span class="post">'
							. wp_specialchars($item['title']) . '</span><span class="hidden"> - </span><cite>'
							. $blog . "</cite></a></li>\n";
				}
				++$rssNum;
			}
		} // foreach ($RSSfeeds as $rssURL)
		$totalItems = 10;
		while ($totalItems > 0) {
			$prevTotal = $totalItems;
			foreach ($rssItems as $i => $items) {
				if ($totalItems > 0 && count($items) > 0) {
					echo array_shift($rssItems[$i]);
					--$totalItems;
				}
			}
			if ($prevTotal == $totalItems) break;
		}
		echo '</ul><br class="clear">';
		echo '<p class="readmore">';
		if (count($rssLinks) > 1) {
			echo 'Read more: ';
			foreach ($rssLinks as $link) {
				echo ' <nobr style="padding-left:1em;"><a href="'
					. $link['link'] . '" target="external">'
					. $link['blog'] . ' &raquo;</a></nobr>';
			}
		}
		else {
			echo '<a href="'.$rssLinks[0]['link'].'" target="external">Read more &raquo;</a>';
		}
		echo '</p>';
	} // if (count($RSSfeeds ...
	echo "</div>";
}

function html_OptinPopConf_Footer() {
return <<<OPTINPOPCONF_FOOTER
<div style="color:gray; font-size:12px; padding-top:1ex;">OptinPop Plug-in
Created by <a href="http://BigSellingOptins.com/Plugin" target="external">BigSellingOptins</a>,
coded by <a href="http://www.icoder.com" target="external">iCoder</a>
</div>
OPTINPOPCONF_FOOTER;
}

?>

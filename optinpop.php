<?php
/*
Plugin Name: OptinPop
Plugin URI: http://BigSellingOptins.com/Plugin
Description: Easily create unblockable popup windows that can display the content of any web page you choose. You can tune the appearance of your pop-up window and other parameters on the OptinPop Settings page within the Plugins menu. Created by <a href="http://BigSellingOptins.com/Plugin">BigSellingOptins.com</a> and <a href="http://iCoder.com">iCoder.com</a>.
Version: 4.1
Revision Date: January 28, 2011
Tested up to: WP 3.0.4
Author: Brian Terry, Michel Komarov
Author URI: http://BigSellingOptins.com

License: Artistic License 2.0

OptinPop Plug-in is released under
Artistic License 2.0
Copyright (c) 2008-2011, ReactorPublishing.com and iCoder.com 

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
	$admin_page = trim(get_option('iOptinPop_Registered'))? 'iOptinPop_conf': 'iOptinPop_regpage';
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('OptinPop Settings'), __('OptinPop Settings')
			, 'manage_options', OPTINPOP_MENU_PAGE, $admin_page);
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
	echo html_OptinPopConf_WindowDetails();
	echo html_OptinPopConf_UpgradeBox();
	//print_html_OptinPopConf_BlogList('Step 3: Profit from your blog');
	//echo html_OptinPopConf_Footer();
	echo '</div>';

} // function iOptinPop_conf()

function iOptinPop_regpage() {
	echo '<div class="wrap"><h2>' . __('OptinPop 4.0') . '</h2>';
	echo html_OptinPopConf_Styles();
	echo html_OptinPopConf_RegisterForm();
	echo html_OptinPopConf_UpgradeBox();
	echo '</div>';
} // function iOptinPop_regpage()

if ( !trim(get_option('iOptinPop_url')) && !isset($_POST['submit']) ) {
	function iOptinPop_warning() {
		echo "
		<div id='iOptinPop-warning' class='updated fade'><p><strong>"
		.__('The OptinPop plugin is not active yet.')."</strong> "
		.sprintf(
			__('You\'ll need to register then <a href="%1$s">enter your pop-up page URL</a> for it to work.')
			, "plugins.php?page=".OPTINPOP_MENU_PAGE)
		."</p></div>
		";
	}
	add_action('admin_notices', 'iOptinPop_warning');
	return;
}

wp_enqueue_script('jquery');

$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '': '.pack';
wp_enqueue_script('fancybox', '/wp-content/plugins/optinpop/fancybox/jquery.fancybox-1.3.1'.$suffix.'.js', array('jquery'), '1.3.1');
wp_enqueue_style( 'fancybox', '/wp-content/plugins/optinpop/fancybox/jquery.fancybox-1.3.1.css', false, '1.3.1');

add_action('wp_head', 'iOptinPop_FancyIssueCSS');
add_action('wp_head', 'iOptinPop_box');
function iOptinPop_box() {
	global $iOptinPop_url;
	$SITECOOKIEPATH = '/';

	$box_showing  = 86400 * intval(get_option('iOptinPop_box_time'));
	$box_width    = intval(get_option('iOptinPop_box_width'));
	$box_height   = intval(get_option('iOptinPop_box_height'));
	$box_lightbox = intval(get_option('iOptinPop_box_lightbox')) > 0? 'true': 'false';
	$box_id       = 'iOptinPopLink'.time();
	$box_time     = time();
	$cookie_expires = date('D, d M Y H:i:s'
		, time() + max($box_showing, 31536000) /* keep cookies at least 1 year */
		) . ' GMT';

	if ('exit' == get_option('iOptinPop_box_show')) {
		$SHOW =<<<SHOW_ON_EXIT
		var iOptinPopBoxStarted = 0;
		var iOptinPopBoxDelay   = 0;
		jQuery(document).mousemove(function(e) {
			tempY = document.all? event.clientY: e.pageY - document.body.scrollTop;
			if (tempY < 0) tempY = 0;
			if (20 > tempY && 0 > --iOptinPopBoxDelay) {
				if (0 == iOptinPopBoxStarted) {
					iOptinPopBoxStarted = 1;
					iOptinPopBox_show();
			}	}
		});
SHOW_ON_EXIT;
	}
	else {
		$SHOW = 'setTimeout("iOptinPopBox_show();", '.(1000 * intval(get_option('iOptinPop_box_delay'))).');';
	}

	$wpurl = get_bloginfo('wpurl');
	if (trim($iOptinPop_url)
	&& (!isset($_COOKIE['iOptinPopBox'])
	|| time() - intval($_COOKIE['iOptinPopBox']) > $box_showing)) {
echo <<<FANCY_BOX
<script type="text/javascript">
<!--//
function iOptinPopBox_show() {
	if ('function' == typeof jQuery.fancybox) iOptinPopBox_showbox();
	else {
		jQuery('#fancybox-wrap,#fancybox-tmp,#fancybox-loading,#fancybox-overlay,#fancybox-outer').remove();
		jQuery.getScript('$wpurl/wp-content/plugins/optinpop/fancybox/jquery.fancybox-1.3.1.js', iOptinPopBox_showbox);
	}
}
function iOptinPopBox_showbox() {
	document.cookie="iOptinPopBox=$box_time; path=$SITECOOKIEPATH; expires=$cookie_expires;";
	jQuery.fancybox('$iOptinPop_url', {
		'width'     : $box_width,
		'height'    : $box_height,
		'type'      : 'iframe',
		'scrolling' : 'no',
		'autoScale' : false,
		'showCloseButton'    : true,
		'enableEscapeButton' : false,
		'overlayOpacity'     : 0.5,
		'overlayShow'        : $box_lightbox,
		'transitionIn'       : 'none',
		'transitionOut'      : 'none',
		'centerOnScroll'     : true,
		'hideOnOverlayClick' : false
	});
}
jQuery(document).ready(function(){
$SHOW
});
//-->
</script>
FANCY_BOX;
	} // if (trim($iOptinPop_url)... && SHOW
} // function iOptinPop_box()

function iOptinPop_FancyIssueCSS() {
$dir = plugin_dir_url( __FILE__ );
echo <<<FancyIssueCSS
<style type="text/css">
#fancybox-loading.fancybox-ie div { background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_loading.png', sizingMethod='scale'); }
.fancybox-ie #fancybox-close { background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_close.png', sizingMethod='scale'); }
.fancybox-ie #fancybox-title-over { background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_title_over.png', sizingMethod='scale'); zoom: 1; }
.fancybox-ie #fancybox-title-left { background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_title_left.png', sizingMethod='scale'); }
.fancybox-ie #fancybox-title-main { background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_title_main.png', sizingMethod='scale'); }
.fancybox-ie #fancybox-title-right { background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_title_right.png', sizingMethod='scale'); }
.fancybox-ie #fancybox-left-ico { background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_nav_left.png', sizingMethod='scale'); }
.fancybox-ie #fancybox-right-ico { background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_nav_right.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-n { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_shadow_n.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-ne { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_shadow_ne.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-e { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_shadow_e.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-se { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_shadow_se.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-s { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_shadow_s.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-sw { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_shadow_sw.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-w { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_shadow_w.png', sizingMethod='scale'); }
.fancybox-ie #fancy-bg-nw { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$dir}fancybox/fancy_shadow_nw.png', sizingMethod='scale'); }
</style>
FancyIssueCSS;
}

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

function html_OptinPopConf_RegisterForm() {
	$user = wp_get_current_user();
	$email = $user->user_email;
	$name = $user->user_nicename;
	if ($name == $user->user_login && trim($user->display_name)) $name = $user->display_name;
	if (isset($_POST['submit'])) {
		$error = '';
		$emailRE = '/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-\.]+$/i';
		if (isset($_POST['name']))  $name  = trim(strip_tags(($_POST['name'])));
		if (isset($_POST['email'])) $email = trim(strip_tags(($_POST['email'])));
		if ('' == $name)  $error .= '<div>Please enter your Name</div>';
		if ('' == $email) $error .= '<div>Please enter your Email</div>';
		if ( ! preg_match($emailRE, $email) ) $error .= '<div>Please enter correct Email address</div>';
		if ('' == $error) {
			$data = array(
				'name'             => $name,
				'email'            => $email,
				'meta_required'    => 'name,email',
				'meta_web_form_id' => '528943826',
				'meta_split_id'    => '',
				'meta_adtracking'  => 'OptinPop_Registration_Form',
				'meta_message'     => '1',
				'meta_tooltip'     => '',
				'listname'         => 'opfree',
				'redirect'         => 'http://www.aweber.com/thankyou-coi.htm?m=text',
			);
			echo '<img src="http://forms.aweber.com/form/displays.htm?id=rEwcnCzMHExs" alt="" />'."\n";
			echo '<iframe name="aweber_frame" src="" width="1" height="1" frameborder="0" marginheight="0" marginwidth="0" scrolling="no"></iframe>'."\n";
			echo '<form action="http://www.aweber.com/scripts/addlead.pl" target="aweber_frame" id="aweber_form" method="post">'."\n";
			foreach ($data as $key => $val) { echo '<input type="hidden" name="'.$key.'" value="'.$val.'" />'."\n"; }
			echo '</form>'."\n";
			echo '<script type="text/javascript">jQuery(document).ready(function(){var f=document.getElementById("aweber_form");if(f)f.submit();});</script>'."\n";
			update_option('iOptinPop_Registered', 'yes');
			echo html_OptinPopConf_WindowDetails();
			return;
		}
	}
return <<<REGISTER_FORM
<style>
.form-table td { padding: 1px; }
.form-table td.reg {
	font-family: Lucida Grande, Arial, sans serif;
	padding-bottom: 1ex;
	font-size: 17px;
	line-height: 22px;
	font-weight: bold;
	color: gray;
}
.form-table td.black { color: #464646; }
div.redsquare {
width: 10px;
height: 10px;
background-color: #e00506;
margin-top: 5px;
margin-right: 4px;
}
</style>
<h3 style="font-size:18px;color:#2583ad;">Please register the plugin to activate it. (Registration is free)</h3>
<div style="padding-left:65px;">
<table class="form-table" style="width:750px;">
<tr><td class="reg black" colspan="2">In addition you'll receive:</td></tr>
<tr><td valign="top"><div class="redsquare">&nbsp;</div></td>
	<td class="reg">Complimentary subscription to the BigSelling Insiders daily newsletter
	<br />giving you the very best listbuilding tips, tricks and offers.</td></tr>
<tr><td valign="top"><div class="redsquare">&nbsp;</div></td>
	<td class="reg">5 plug and play listbuilding templates to make settings up your
	<br />popup windows quick and easy.</td></tr>
<tr><td valign="top"><div class="redsquare">&nbsp;</div></td>
	<td class="reg">23 methods to promoting your blog, generating hordes of new
	<br />subscribers all from your content... in just 30 minutes per day.</td></tr>
<tr><td valign="top"><div class="redsquare">&nbsp;</div></td>
	<td class="reg">And much, much more...</td></tr>
</table>
&nbsp;
<form method="post" action="" onsubmit="return checkRegisterForm(this);">
<table class="form-table" style="width:750px;">
<tr><td class="reg black" colspan="2" noWrap="noWrap">Step 1: Register now for instant plugin activation</td></tr>
<tr><td class="reg" colspan="2" style="color:red;" align="right">$error</td></tr>
<tr><td colspan="2">
	<table class="form-table" style="width:auto;">
	<tr class="form-field"><th style="width:150px;"><label for="ar_name">Name:</label></th>
		<td><input type="text" name="name" value="$name" id="ar_name" size="25" style="width:220px;" /></td></tr>
	<tr class="form-field"><th style="width:150px;"><label for="ar_email">Email:</label></th>
		<td><input type="text" name="email" value="$email" id="ar_email" size="25" style="width:220px;" /></td></tr>
	<tr><td>&nbsp;</td><td class="submit"><input type="submit" name="submit" value="Register" /></td></tr>
	</table>
</td></tr>
<tr><td class="reg black" colspan="2" noWrap="noWrap" style="padding-top:2em;">Step 2: Check your inbox for a confirmation email</td></tr>
</table>
</form>
<div style="width:750px;padding-top:1em;">
Your contact information will be handled with the strictest confidence and will never be sold or
<br />shared with third parties. Also, you can unsubscribe at anytime.
</div>
</div>
<script type="text/javascript">
<!--//
function checkRegisterForm(theForm) {
	var error = '';
	if (theForm) {
		if (theForm['name']  && '' == theForm['name'].value)  error = 'Please enter your Name';
		if (theForm['email'] && '' == theForm['email'].value) error = 'Please enter your Email';
	}
	if ('' != error) {
		alert (error);
		return false;
	}
	return true;
}
//-->
</script>
REGISTER_FORM;
} // function html_OptinPopConf_RegisterForm($header)

function html_OptinPopConf_WindowDetails() {
	$iOptinPop_url        = get_option('iOptinPop_url');
	$iOptinPop_box_width  = get_option('iOptinPop_box_width');  if (empty($iOptinPop_box_width))  $iOptinPop_box_width  = 600;
	$iOptinPop_box_height = get_option('iOptinPop_box_height'); if (empty($iOptinPop_box_height)) $iOptinPop_box_height = 500;
	$iOptinPop_box_delay  = get_option('iOptinPop_box_delay');  if (empty($iOptinPop_box_delay))  $iOptinPop_box_delay  = 0;
	$iOptinPop_box_time   = get_option('iOptinPop_box_time');   if (empty($iOptinPop_box_time))   $iOptinPop_box_time   = 1;
	$iOptinPop_box_show_exit  = 'exit' == get_option('iOptinPop_box_show')? 'checked': '';
	$iOptinPop_box_show_load  = 'exit' != get_option('iOptinPop_box_show')? 'checked': '';
	$iOptinPop_box_lightbox_0 = intval(get_option('iOptinPop_box_lightbox')) > 0? '': 'checked';
	$iOptinPop_box_lightbox_1 = intval(get_option('iOptinPop_box_lightbox')) > 0? 'checked': '';
	$iOptinPop_box_time_0     = 0 == intval(get_option('iOptinPop_box_time'))? 'checked': '';
	$iOptinPop_box_time_1     = intval(get_option('iOptinPop_box_time')) > 0 ? 'checked': '';
return <<<WINDOW_DETAILS
<form action="" method="post" id="iOptinPop-conf"><table id="window-details" class="form-table" style="width:750px;"><tbody>
<tr class="form-field"><th><label for="OptinPop_url">URL of the page to show</label></th>
	<td colspan="2" align="right"><input id="OptinPop_url" name="OptinPop_url" value="$iOptinPop_url"
		type="text" size="50" style="width:99%;" /></td></tr>
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
		<label for="OptinPop_time_days">Days</label></td></tr>
<tr><td colspan="3" class="submit" align="right">
		<input type="submit" name="submit" value="Update options &raquo;" /></td></tr>
</tbody></table>
</form>
WINDOW_DETAILS;
} // function html_OptinPopConf_WindowDetails($header)

function html_OptinPopConf_UpgradeBox() {
echo <<<UPGRADE_BOX
<style type="text/css">
#optinpop-upgrade-link-wrap {
float:right;height:36px;padding:0;margin:0 6px 0 0; background:#e00506;
font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
-moz-border-radius-bottomleft:5px;-moz-border-radius-bottomright:5px;-webkit-border-bottom-left-radius:5px;-webkit-border-bottom-right-radius:5px;
}
#optinpop-upgrade-link {
text-decoration:none;z-index:1; font-size:17px;color:#ffffff;
padding:2px 14px 2px 14px;height:24px;line-height:29px;display:block;
}
#optinpop-upgrade-wrap {
border-style:none solid solid;border-top:0 none;border-width:0 1px 1px;margin:0 15px;padding:8px 12px 12px;
-moz-border-radius:0 0 0 4px;-webkit-border-bottom-left-radius:4px;-khtml-border-bottom-left-radius:4px;border-bottom-left-radius:4px;
background-color:#ffffff;border-color:#dfdfdf;
}
</style>
<p class="submit" style="font-size:1ex;">&nbsp;</p>
<div id="screen-meta">
<div id="optinpop-upgrade-wrap" class="hidden">
<iframe id="optinpop-upgrade-iframe" src="" style="width:100%;height:620px;"></iframe>
</div>
<div id="screen-meta-links" style="width:740px;">
<div id="optinpop-upgrade-link-wrap" class="hide-if-no-js screen-meta-toggle">
<a href="#optinpop-upgrade" id="optinpop-upgrade-link">Upgrade <span id="optinpop-upgrade-mark">&rsaquo;</span></a>
</div>
</div>
</div>
<script type="text/javascript">
<!--//
var optinpop_upgrade_URL = "http://bigsellingoptins.com/optinpopadvanced.iframe/index.html";
jQuery(document).ready(function(d){d("#optinpop-upgrade-link").click(function(){if(!d("#optinpop-upgrade-wrap").hasClass("optinpop-upgrade-open")){d("#screen-options-link-wrap").css("visibility","hidden")}d("#optinpop-upgrade-wrap").slideToggle("fast",function(){if(d(this).hasClass("optinpop-upgrade-open")){d("#optinpop-upgrade-mark").html("&rsaquo;");d("#screen-options-link-wrap").css("visibility","");d(this).removeClass("optinpop-upgrade-open")}else{d("#optinpop-upgrade-mark").html("&lsaquo;");d(this).addClass("optinpop-upgrade-open");d("#optinpop-upgrade-iframe").attr("src",optinpop_upgrade_URL);}});return false});});
//-->
</script>
UPGRADE_BOX;
} // function html_OptinPopConf_UpgradeBox() {

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

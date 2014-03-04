<?php
/*
Plugin Name: tian yi weather
Author:  jhzhang
Author URI: ddd
Plugin URI: dssddd
Description: ty weather report。
Version: 2.3.2
*/
$tw_consumer_key = '397388240000032128';
$tw_consumer_secret = 'ac58c160d3536593d31a4a72bf0dc94a';

$tw_access_token = isset($_SESSION['tytoken']) ? $_SESSION['tytoken']['access_token'] : '44e009a90c5b92c45bcc686903c338b81388113884713';
function ty_weather_report () {
	do_action('ty_weather_report');
	
}

add_action('ty_weather_report', 'cus_weather');

function cus_weather () {
	global $tw_access_token, $tw_consumer_secret, $tw_consumer_key;
	//echo dirname (dirname (__FILE__));
	if (!class_exists('TYOAuth')) {
		include_once dirname (dirname (__FILE__)). '/tianyi/TYOAuth.php';
	}
	
	$ty = new TYOAuth($tw_consumer_key, $tw_consumer_secret);
	
	$info = $ty -> weatherReport($tw_access_token);
	$doc = new DOMDocument();
	$doc->loadXML ($info);
	
	$nodes = $doc ->getElementsByTagName ('forecast');
	if (count($nodes)) {
		
		foreach ($nodes as $node) {
			$w = '<img style="height:30px;" src="'.WP_PLUGIN_URL . '/ty-weather/tylogo.gif' . '" />今日上海天气: ';
			$w .= $node -> getAttribute ('DATE') .', '. $node -> getAttribute ('WEA').', '. $node -> getAttribute ('WIND').', 最高温度:  '. $node -> getAttribute ('TMAX'). ', 最低温度:  '. $node -> getAttribute ('TMIN');
		}
	}
	
	echo '<div style="position:relative; max-width:960px;top:35px;clear:both;margin:0 auto;">';
	echo $w;
	echo '</div>';
	
}

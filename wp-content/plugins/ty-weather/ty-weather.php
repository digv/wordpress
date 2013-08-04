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

$tw_access_token = isset($_SESSION['tytoken']) ? $_SESSION['tytoken']['access_token'] : 'b793f8e0c0f9da3845a89f87a07e084d1374583138463';

function ty_weather_report () {
	do_action('ty_weather_report');
	
}

add_action('ty_weather_report', 'cus_weather');

function cus_weather () {
	
	if (!class_exists('TYOAuth')) {
		include_once WP_PLUGIN_URL .'/tianyi/TYOAuth.php';
	}
	
	$ty = new TYOAuth($tw_consumer_key, $tw_consumer_secret);
	
	$info = $ty -> weatherReport('', $tw_access_token);
	var_dump($info);
	
}
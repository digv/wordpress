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

$tw_access_token = isset($_SESSION['tytoken']) ? $_SESSION['tytoken']['access_token'] : '3cdaed895e096e0776d19de10bdf48ac1374754416322';

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
	
	$info = $ty -> weatherReport('', $tw_access_token);
	var_dump($info);
	
}
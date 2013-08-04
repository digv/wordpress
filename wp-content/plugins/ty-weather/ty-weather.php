<?php
/*
Plugin Name: tian yi weather
Author:  jhzhang
Author URI: ddd
Plugin URI: dssddd
Description: ty weather report。
Version: 2.3.2
*/


function ty_weather_report () {
	do_action('ty_weather_report');
	
}

add_action('ty_weather_report', 'cus_weather');

function cus_weather () {
	echo '<div class="diy" style="width:100px; position:absolute;left:300px;">tian yi weather</div>';
}
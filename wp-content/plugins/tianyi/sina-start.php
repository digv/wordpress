<?php
include "../../../wp-config.php";

if(!class_exists('TYOAuth')){
	include dirname(__FILE__).'/TYOAuth.php';
}
//var_dump ('dd');
$to = new TYOAuth($ty_consumer_key, $ty_consumer_secret);

	


if (isset($_GET['code'])) {
//var_dump ($_REQUEST['code'], 'zjh');
$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = TY_CALLBACK_URL;
	try {
		$token = $to->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	var_dump ($e);
	}
}

if ($token) {
	$_SESSION['tytoken'] = $token;
	setcookie( 'tianyi_'.$o->app_id, http_build_query($token) );
}
//var_dump($token, 'zjh2');
if($_GET['callback_url']){
	$callback_url = $_GET['callback_url'];
}else{
	$callback_url = get_option('home');
}

header('Location:'.$callback_url);
?>

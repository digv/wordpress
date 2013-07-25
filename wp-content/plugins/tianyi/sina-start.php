<?php
include "../../../wp-config.php";

if(!class_exists('SinaOAuth')){
	include dirname(__FILE__).'/sinaOAuth.php';
}
//var_dump ('dd');
$to = new SinaOAuth($sina_consumer_key, $sina_consumer_secret);

	


if (isset($_GET['code'])) {
//var_dump ($_REQUEST['code'], 'zjh');
$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $to->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	var_dump ($e);
	}
}

if ($token) {
	$_SESSION['token'] = $token;
	setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
}
//var_dump($token, 'zjh2');
if($_GET['callback_url']){
	$callback_url = $_GET['callback_url'];
}else{
	$callback_url = get_option('home');
}


header('Location:'.$callback_url);
?>

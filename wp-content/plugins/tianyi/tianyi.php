﻿<?php
/*
Plugin Name: 天翼连接
Author:  Denis
Author URI: http://fairyfish.net/
Plugin URI: http://fairyfish.net/2010/06/08/sina-connect/
Description: 使用新浪微博瓣账号登陆你的 WordPress 博客，并且留言使用新浪微博的头像，博主可以同步日志到新浪微博，用户可以同步留言到新浪微博。
Version: 2.3.2
*/
$ty_consumer_key = '397388240000032128';
$ty_consumer_secret = 'ac58c160d3536593d31a4a72bf0dc94a';
$ty_loaded = false;
define(TY_CALLBACK_URL ,  WP_PLUGIN_URL.'/'.dirname(plugin_basename (__FILE__)). '/sina-start.php');
add_action('init', 'ty_init');
function ty_init(){
	if (session_id() == "") {
		session_start();
	}
	if(!is_user_logged_in()) {		
        if(isset($_GET['code'])){
			ty_confirm();
        } 
    } 
}

add_action("wp_head", "ty_wp_head");
add_action("admin_head", "ty_wp_head");
add_action("login_head", "ty_wp_head");
add_action("admin_head", "ty_wp_head");
function ty_wp_head(){
    if(is_user_logged_in()) {
        if(isset($_GET['oauth_token'])){
			echo '<script type="text/javascript">window.opener.ty_reload("");window.close();</script>';
        }
	}

echo <<<html
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-43951039-1', 'digv.cn');
  ga('send', 'pageview');

</script>

html;
}

add_action('comment_form', 'ty_connect');
add_action("login_form", "ty_connect");
add_action("register_form", "ty_connect",12);
function ty_connect($id='',$callback_url=''){
	global $ty_loaded, $ty_consumer_key, $ty_consumer_secret;
	if($ty_loaded) {
		return;
	}
	if(is_user_logged_in() && !is_admin()){
		return;
	}
	$ty_url = WP_PLUGIN_URL.'/'.dirname(plugin_basename (__FILE__));
	 if(!class_exists('TYOAuth')){
                include dirname(__FILE__).'/TYOAuth.php';
        }
	$o = new TYOAuth( $ty_consumer_key, $ty_consumer_secret);

	$code_url = $o->getAuthorizeURL( TY_CALLBACK_URL );	
?>
	<script type="text/javascript">
    function ty_reload(){
       var url=location.href;
       var temp = url.split("#");
       url = temp[0];
       url += "#ty_button";
       location.href = url;
       location.reload();
    }
    </script>	
	<style type="text/css"> 
	.ty_button img{ border:none;}
    </style>
	<p id="ty_connect" class="ty_button">
	<img onclick='window.open("<?php echo $code_url; ?>", "dcWindow","width=800,height=600,left=150,top=100,scrollbar=no,resize=no");return false;' src="<?php echo $ty_url; ?>/ty.jpg" alt="使用天翼账户登陆" style="cursor: pointer; margin-right: 20px;" />
	</p>
<?php
    $ty_loaded = true;
}

add_filter("get_avatar", "ty_get_avatar",10,4);
function ty_get_avatar($avatar, $id_or_email='',$size='32') {
	global $comment;
	if(is_object($comment)) {
		$id_or_email = $comment->user_id;
	}
	if (is_object($id_or_email)){
		$id_or_email = $id_or_email->user_id;
	}
	if($scid = get_usermeta($id_or_email, 'scid')){
		
$out = 'http://tp3.sinaimg.cn/'.$scid.'/50/1.jpg';
		$avatar = "<img alt='' src='{$out}' class='avatar avatar-{$size}' height='{$size}' width='{$size}' />";
		return $avatar;
	}else {
		return $avatar;
	}
}

function ty_confirm(){
    global $ty_consumer_key, $ty_consumer_secret;
	
	if(!class_exists('TYOAuth')){
		include dirname(__FILE__).'/TYOAuth.php';
	}
$to = new TYOAuth($ty_consumer_key, $ty_consumer_secret);
if (isset($_GET['code'])) {
//var_dump ($_REQUEST['code'], 'zjh');
$keys = array();
        $keys['code'] = $_REQUEST['code'];
        $keys['redirect_uri'] = TY_CALLBACK_URL;
        try {
                $token = $to->getAccessToken( 'code', $keys ) ;
        } catch (OAuthException $e) {
  //      var_dump ($e);
        }
}

if ($token) {
        $_SESSION['tytoken'] = $token;
        setcookie( 'ty_'.$to->app_id, http_build_query($token) );
}

//var_dump($_SESSION['tytoken']);
	$ty_user_name = $_SESSION['tytoken']['open_id'];
		
	ty_login($_SESSION['tytoken']['open_id'].'|'.$ty_user_name.'|'.'天翼用户_'.$_SESSION['tytoken']['open_id'].'|'.''.'|'. $_SESSION['tytoken']['access_token'] .'|'. ''); 
}

function ty_login($Userinfo) {
	$userinfo = explode('|',$Userinfo);
	if(count($userinfo) < 6) {
		wp_die("An error occurred while trying to contact Sina Connect.");
	}

	$userdata = array(
		'user_pass' => wp_generate_password(),
		'user_login' => 'tianyi_'. $userinfo[1],
		'display_name' => $userinfo[2],
		'user_url' => $userinfo[3],
		'user_email' => $userinfo[1].'@weibo.com'
	);

	if(!function_exists('wp_insert_user')){
		include_once( ABSPATH . WPINC . '/registration.php' );
	} 
  
	//$wpuid = get_user_by_login($userinfo[1]);
	$wpuid =get_user_by_meta('scid', ($userinfo[0]));

	if(!$wpuid){
		if($userinfo[0]){
			$wpuid = wp_insert_user($userdata);
		
			if($wpuid){
				update_user_meta($wpuid, 'scid', $userinfo[0]);
				$ty_array = array (
					"oauth_access_token" => $userinfo[4],
					"oauth_access_token_secret" => $userinfo[5],
				);
				update_user_meta($wpuid, 'scdata', $ty_array);
			}
		}
	} else {
		update_user_meta($wpuid, 'scid', $userinfo[0]);
		$ty_array = array (
			"oauth_access_token" => $userinfo[4],
			"oauth_access_token_secret" => $userinfo[5],
		);
		update_user_meta($wpuid, 'scdata', $ty_array);
	}
  
	if($wpuid) {
		wp_set_auth_cookie($wpuid, true, false);
		wp_set_current_user($wpuid);
	}
}

function ty_sinauser_to_wpuser($scid) {
  return get_user_by_meta('scid', $scid);
}

if(!function_exists('get_user_by_meta')){

	function get_user_by_meta($meta_key, $meta_value) {
	  global $wpdb;
	  $sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '%s' AND meta_value = '%s'";
	  return $wpdb->get_var($wpdb->prepare($sql, $meta_key, $meta_value));
	}

	
	function get_user_by_login($user_login) {
	  global $wpdb;
	  $sql = "SELECT ID FROM $wpdb->users WHERE user_login = '%s'";
	  return $wpdb->get_var($wpdb->prepare($sql, $user_login));
	}
}

if(!function_exists('connect_login_form_login')){
	add_action("login_form_login", "connect_login_form_login");
	add_action("login_form_register", "connect_login_form_login");
	function connect_login_form_login(){
		if(is_user_logged_in()){
			$redirect_to = admin_url('profile.php');
			wp_safe_redirect($redirect_to);
		}
	}
}

add_action('comment_post', 'ty_comment_post',1000);
function ty_comment_post($id){
	$comment_post_id = $_POST['comment_post_ID'];
	
	if(!$comment_post_id){
		return;
	}
	$current_comment = get_comment($id);
	$current_post = get_post($comment_post_id);
	$scdata = get_user_meta($current_comment->user_id, 'scdata',true);
	if($scdata){
		if($_POST['post_2_ty_t']){
			if(!class_exists('TYOAuth')){
				include dirname(__FILE__).'/TYOAuth.php';
			}
			global $ty_consumer_key, $ty_consumer_secret;
			$to = new TYOAuth($ty_consumer_key, $ty_consumer_secret,$scdata['oauth_access_token'], $scdata['oauth_access_token_secret']);
			$status = urlencode($current_comment->comment_content. ' '.get_permalink($comment_post_id)."#comment-".$id);			
			$resp = $to->OAuthRequest('http://api.t.sina.com.cn/statuses/update.xml','POST',array('status'=>$status));		
		}
	}
}

add_action('admin_menu', 'ty_options_add_page');

function ty_options_add_page() {
	add_options_page('同步到新浪微博', '同步到新浪微博', 'manage_options', 'ty_options', 'ty_options_do_page');
}

function ty_options_do_page() {
	if($_GET['delete']) {
		delete_option('sina_access_token');
	}elseif(isset($_GET['oauth_token'])){
		global $ty_consumer_key, $ty_consumer_secret;
	
		if(!class_exists('SinaOAuth')){
			include dirname(__FILE__).'/sinaOAuth.php';
		}
		
		$to = new SinaOAuth($ty_consumer_key, $ty_consumer_secret, $_GET['oauth_token'],$_SESSION['sina_oauth_token_secret']);
		
		$tok = $to->getAccessToken($_REQUEST['oauth_verifier']);
		update_option('sina_access_token',$tok);
	}
	?>
	<div class="wrap">
		<h2>同步到新浪微博</h2>
		<form method="post" action="options.php">
            <?php
			if($_GET['delete']){
				 echo '<p>你已经删除了原来绑定的新浪微博帐号了。<a href="'.menu_page_url('ty_options',false).'">重新绑定或者绑定其他帐号？</a></p>';
			} else {
				if($tok = get_option('sina_access_token')){
					
					if(!class_exists('SinaOAuth')){
						include dirname(__FILE__).'/sinaOAuth.php';
					}
					
					global $ty_consumer_key, $ty_consumer_secret;
					
					$to = new SinaOAuth($ty_consumer_key, $ty_consumer_secret, $tok['oauth_token'], $tok['oauth_token_secret']);
					
					$sinaInfo = $to->OAuthRequest('http://api.t.sina.com.cn/account/verify_credentials.xml', 'GET',array());
					$sinaInfo = simplexml_load_string($sinaInfo);

					if((string)$sinaInfo->domain){
						$ty_user_name = $sinaInfo->domain;
					} else {
						$ty_user_name = $sinaInfo->id;
					}
					echo '<p>你已经绑定了新浪微博帐号 <a href="http://weibo.com/'.$ty_user_name.'">'.$sinaInfo->screen_name.'</a> 了。<a href="'.menu_page_url('ty_options',false).'&delete=1">删除绑定或者绑定其他帐号？</a></p>';
				}else{
					echo '<p>点击下面的图标，将你的新浪微博客帐号和你的博客绑定，当你的博客更新的时候，会同时更新到新浪微博。</p>';
					ty_connect('',menu_page_url('ty_options',false));
				}
			}
			?>
	</div>
	<?php
}

function update_ty_t($status=null){
	$tok = get_option('sina_access_token');
	if(!class_exists('SinaOAuth')){
		include dirname(__FILE__).'/sinaOAuth.php';
	}
	global $ty_consumer_key, $ty_consumer_secret;
	$to = new SinaOAuth($ty_consumer_key, $ty_consumer_secret,$tok['oauth_token'], $tok['oauth_token_secret']);
	$status = urlencode($status);
	$resp = $to->OAuthRequest('http://api.t.sina.com.cn/statuses/update.xml','POST',array('status'=>$status));
}

function upload_ty_t($status,$pic){
	if(!$pic) return;
	$tok = get_option('ty_access_token');
	if(!class_exists('SinaOAuth')){
		include dirname(__FILE__).'/sinaOAuth.php';
	}
	global $ty_consumer_key, $ty_consumer_secret;
	$to = new SinaOAuth($ty_consumer_key, $ty_consumer_secret,$tok['oauth_token'], $tok['oauth_token_secret']);

	$status = urlencode($status);
	
	$resp = $to->post('http://api.t.sina.com.cn/statuses/upload.json',array('status'=>$status,'pic'=>'@'.$pic),true);
}


add_action('publish_post', 'publish_post_2_ty_t', 0);
function publish_post_2_ty_t($post_ID){
	$tok = get_option('sina_access_token');
	if(!$tok) return;
	$ty_t = get_post_meta($post_ID, 'ty_t', true);
	if($ty_t) return;
	$c_post = get_post($post_ID);
	//$status = $c_post->post_title.' '.get_permalink($post_ID);
	
	$post_title = $c_post->post_title;
	$post_content = get_post_excerpt($c_post);

	$title_len = mb_strlen($post_title,'UTF-8');
	$content_len = mb_strlen($post_content,'UTF-8');
	$rest_len = 120;

	if($title_len + $content_len> $rest_len) {
		$post_content = mb_substr($post_content,0,$rest_len-$title_len).'... ';
	}
	$status = '【'.$post_title.'】 '.$post_content.get_sina_short_url(get_permalink($post_ID));

	$pic = get_post_first_image($c_post->post_content);
	
	if($pic){
		upload_ty_t($status,$pic);
	}else{
		update_ty_t($status);
	}

	update_ty_t($status);
	add_post_meta($post_ID, 'ty_t', 'true', true);
}

if(!function_exists('get_post_first_image')){

	function get_post_first_image($post_content){
		preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post_content, $matches);
		if($matches){		
			return $matches[1][0];
		}else{
			return false;
		}
	}
}
if(!function_exists('get_sina_short_url')){

	function get_sina_short_url($long_url){
		$api_url = 'http://api.t.sina.com.cn/short_url/shorten.json?source=744243473&url_long='.$long_url;
		$request = new WP_Http;
		$result = $request->request( $api_url);
		$result = $result['body'];
		$result = json_decode($result);
		return $result[0]->url_short;
	}
}

if(!function_exists('get_post_excerpt')){
	function get_post_excerpt($post){
		$post_excerpt = strip_tags($post->post_excerpt); 
		if(!$post_excerpt){
			###第一种情况，以<p>开始,</p>结束来取第一段 Windows live writer
			if(preg_match('/<p>(.*)<\/p>/iU',trim(strip_tags($post->post_content,"<p>")),$result)){ 
				$post_content = $result['1'];
			} else {
			###第二种情况，以换行符(\n)来取第一段   
				$post_content_r = explode("\n",trim(strip_tags($post->post_content))); 
				$post_content = $post_content_r['0'];
			}
			$post_excerpt = explode("\n",trim(strip_tags($post->post_content))); 
   			$post_excerpt = $post_excerpt['0'];	
		}
		$post_excerpt = trim(strip_tags($post_excerpt));
		$post_excerpt = str_replace('"', '', $post_excerpt);	
		// replace newlines on mac / windows?
		$post_excerpt = str_replace("\r\n", ' ', $post_excerpt);
		// maybe linux uses this alone
		$post_excerpt = str_replace("\n", ' ', $post_excerpt);
		$post_excerpt = mb_substr($post_excerpt,0,120);

		return $post_excerpt;
	}
}

if(!function_exists('wpjam_modify_dashboard_widgets')){
	
	add_action('wp_dashboard_setup', 'wpjam_modify_dashboard_widgets' );
	function wpjam_modify_dashboard_widgets() {
		global $wp_meta_boxes;
		
		wp_add_dashboard_widget('wpjam_dashboard_widget', '我爱水煮鱼', 'wpjam_dashboard_widget_function');
	}
	
	function wpjam_dashboard_widget_function() {?>
		<p><a href="http://wpjam.com/&amp;utm_medium=wp-plugin&amp;utm_campaign=wp-plugin&amp;utm_source=<?php bloginfo('home');?>" title="WordPress JAM" target="_blank"><img src="http://wpjam.com/wp-content/themes/WPJ-Parent/images/logo_index_1.png" alt="WordPress JAM"></a><br />
        <a href="http://wpjam.com/&amp;utm_medium=wp-plugin&amp;utm_campaign=wp-plugin&amp;utm_source=<?php bloginfo('home');?>" title="WordPress JAM" target="_blank"> WordPress JAM</a> 是中国最好的 WordPress 二次开发团队，我们精通 WordPress，可以制作 WordPress 主题，开发 WordPress 插件，WordPress 整站优化。</p>
        <hr />
	<?php 
		echo '<div class="rss-widget">';
		wp_widget_rss_output('http://fairyfish.net/feed/', array( 'show_author' => 0, 'show_date' => 1, 'show_summary' => 0 ));
		echo "</div>";
	}
}

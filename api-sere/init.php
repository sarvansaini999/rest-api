<?php 
/*
$host=strtolower($_SERVER['HTTP_HOST']);

/*serenity-terraces 301 Redirect *//*
if($host =='serenity-terraces.com' || $host =='www.serenity-terraces.com'){
	$redirect_url = "http://" .($host =='www.serenity-terraces.com' ? 'www.': '') .'serenityphuket.com'  . $_SERVER['REQUEST_URI'];

	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . $redirect_url);
	exit;		
}


/*to redirect serenityphuket.com to www.serenityphuket.com*//*
if($host == 'serenityphuket.com'){
	$redirect_url = 'http://www.serenityphuket.com'  . $_SERVER['REQUEST_URI'];

	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . $redirect_url);
	exit;		

}
	

/*to redirect serenity_garden_view_suite.php to serenity_suite.php *//*
$redirect_filename_array=explode('/',$_SERVER['REQUEST_URI']);

$redirect_filename= strtolower($redirect_filename_array[sizeof($redirect_filename_array) -1]);

if('serenity_garden_view_suite.php' == $redirect_filename){
	$redirect_url = 'http://www.serenityphuket.com/'  . 'serenity_suite.php';

	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . $redirect_url);
	exit;		
}
*/
/*enforce https on contact-information.php, billing-information.php and view-summary.php*/

/*
if($redirect_filename == 'contact-information.php' || $redirect_filename == 'wedding_form.php'){
	
	$secure_connection = false;
	
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'     || $_SERVER['SERVER_PORT'] == 443) {
		$secure_connection = true;
	}
	
	if(!$secure_connection){	
		$redirect_url='https://www.serenityphuket.com/'  . $redirect_filename;
		header('Location: ' . $redirect_url);
		exit;		
	}	
}
*/
session_start();
error_reporting(0);
include('./includes/common.parameter.php');
include('./includes/common.database.php');	
include('./includes/functions/functions.php');	


/*
*exclude from tracking
* value of 1 means include in tracking
*/
$track_bookings=1;
if(isset($_GET['debug']) && $_GET['debug'] =='1'){
	$_SESSION['debug']=1;
	$track_bookings=0;
}else if(isset($_SESSION['debug']) && $_SESSION['debug']=='1'){
	$track_bookings=0;
}

/*
* affliate tracking
* if we have ref in querystring save it in a cookie for 30 days
*/
if(isset($_GET['ref']) && $_GET['ref'] !=''){
	setcookie("ref", $_GET['ref'], time()+60*60*24*30, "/", ".serenityphuket.com");
}

/*
* save booking dates and other data
* so that user do not have enter it again
* set expiration to 1 Day
*/

$chk_in_cookie=get_value_post_get('chk_in');
$chk_out_cookie=get_value_post_get('chk_out');
$adults_cookie=get_value_post_get('selAdults');
if($adults_cookie ==''){
	$adults_cookie="2";
}

$children_cookie=get_value_post_get('selChildren');
if('' == $children_cookie){
	$children_cookie='0';
}

if($chk_in_cookie != '' && $chk_out_cookie !=''){
	//check if check in and checkout are valid dates
	
	if(valid_date($chk_in_cookie) && valid_date($chk_out_cookie)){
		setcookie("chk_in", $chk_in_cookie, time()+60*60*24, "/", ".serenityphuket.com");
		setcookie("chk_out", $chk_out_cookie, time()+60*60*24, "/", ".serenityphuket.com");
		setcookie("selAdults", $adults_cookie, time()+60*60*24, "/", ".serenityphuket.com");
		setcookie("selChildren", $children_cookie, time()+60*60*24, "/", ".serenityphuket.com");
	}
	
}

/*
$current_filename_array=explode('/',$_SERVER['REQUEST_URI']);
$current_filename=$current_filename_array[sizeof($current_filename_array) -1];
*/
$current_filename=basename($_SERVER['PHP_SELF']);
?>
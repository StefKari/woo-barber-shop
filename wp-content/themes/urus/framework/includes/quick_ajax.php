<?php
//mimic the actuall admin-ajax
define('DOING_AJAX', true);
$allowed_actions = array(
    'urus_live_search',
	'urus_all_products',
    'urus_ajax_login',
    'urus_quick_view'
);

//make sure you update this line
//to the relative location of the wp-load.php
require_once('../../../../../wp-load.php');
if (isset($_POST['action'])){
	$action = $_POST['action'];
}elseif (isset($_GET['action'])){
	$action = $_GET['action'];
}
if (!in_array($action,$allowed_actions))
	die(-1);

//Typical headers
header('Content-Type: text/html');
send_nosniff_header();

//Disable caching
header('Cache-Control: no-cache');
header('Pragma: no-cache');

//A bit of security
/*
//For logged in users

//For guests
*/

    if(is_user_logged_in())
        do_action('familab_ajax_'.$action);
    else
        do_action('familab_ajax_nopriv_'.$action);
?>
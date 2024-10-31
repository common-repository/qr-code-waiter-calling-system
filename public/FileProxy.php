<?php

require_once( '../../../../wp-config.php' );
require_once('../includes/db.php');
require_once('../includes/class-public-qr-code-waiter-calling-system.php');

if ( ! defined( 'ABSPATH' ) ) {
	/** Set up WordPress environment */
	require_once( dirname( __FILE__ ).'/wp-load.php' );
}

/**
 * QR Code Waiter Calling System File Proxy
 *
 * @package   QRCodeWaiterCallingSystem
 * @author    Catkin
 */


//global variables

$data   = "";
$method = "";
$result = false;


//extract data from request
if ( isset( $_REQUEST["data"] ) ) {
	$data = $_REQUEST["data"];
}
//extract method from request
if ( isset( $_REQUEST["method"] ) ) {
	$method = $_REQUEST["method"];
}
$api_key = QRCodeWaiterCallingSystem_Public::getAPIkey();
$url     = "https://wpapi20200511002740.azurewebsites.net/Action/".$api_key;
//load remote file
if ( $url != "" ) {
	$data = str_replace( "\\\"", "\"", $data );
	//set options/ headers
	$options = array(
		'http' => array(
			'method'  => $method,
			'content' => $data,
			'header'  => "Content-Type: application/json\r\n".
			             "Accept: application/json\r\n"
		)
	);

	//call api
	$result = request( $url, $method, $data );
}

//print content or error message
if ( isset($result['Error'])) {

	//return file content
	echo "An error has occurred.";
} else {

	echo $result[0];
}

function request( $url, $method = GET, $params_string = null ) {
	$request = new WP_Http;
	$output  = $request->request(
		$url.( ( $method === GET || $method === DELETE ) ? $params_string : '' ),
		array(
			'method'  => $method,
			'body'    => $params_string,
			'headers' => array(
				'Content-Type' => 'application/json; charset=utf-8'
			)
		) );
	if ( is_wp_error( $output ) ) {
		return array(
			'Error'    => __( 'Waiter Calling System service is not available at this time. Try again later or contact us directly.', 'redi-restaurant-reservation' ),
			'Wp-Error' => $output->errors
		);
	}

	if ( $output['response']['code'] != 200 && $output['response']['code'] != 400 ) {
		return array( 'Error' => __( 'Waiter Calling System service is not available at this time. Try again later or contact us directly.', 'redi-restaurant-reservation' ) );
	}
	$output = $output['body'];

	// convert response
	$output = (array) json_decode( $output );

	return $output;
}
function unescape_array( $array ) {
	$unescaped_array = array();
	foreach ( $array as $key => $val ) {
		if ( is_array( $val ) ) {
			$unescaped_array[ $key ] = unescape_array( $val );
		} else {
			$unescaped_array[ $key ] = stripslashes( $val );
		}
	}

	return $unescaped_array;
}
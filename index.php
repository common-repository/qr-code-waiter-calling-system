<?php
/**
 * Represents the actual page for displaying "QR Code Waiter Calling System".
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package QR Code Waiter Calling System
 * @author  Catkin <catkin@catzsoft.ee>
 * @author  robby.roboter <roboter@begemotik.ee>
 */
 
    //wp include
    
	//start
    include_once('../../../wp-config.php');

	global $wpdb;
	
	//wp db includes
	require_once( ABSPATH . WPINC . '/wp-db.php' );
	if ( file_exists( WP_CONTENT_DIR . '/db.php' ) )
		require_once( WP_CONTENT_DIR . '/db.php' );

	if (!isset( $wpdb ) )
		$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
	
	//plugin db include
 	require('includes/db.php'); 
	
	//class include
	require_once('includes/class-public-qr-code-waiter-calling-system.php'); 
 

	$site_name     = get_bloginfo('name'); 
	$page_active   = QRCodeWaiterCallingSystem_Public::isActive();
   
	if(!$page_active)
   		exit('Not activated!');
		
	$logo_filename = QRCodeWaiterCallingSystem_Public::getLOGOfilename();
    $api_call_url  = QRCodeWaiterCallingSystem_Public::getAPIcallURL() . "&method=POST&data=";
	$error_result  = 'An error has occurred.';
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $site_name;?> - Waiter Calling System</title>
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" href="//code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css">
        <script src="//code.jquery.com/jquery-1.8.2.min.js"></script>
        <script src="//code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
    </head>
    <body>
        <div data-role="page" data-theme="b" id="home" style="text-align: center">
	        <div data-role="header">
		        <h1><?php echo $site_name;?></h1>
	        </div>
            <?php if($logo_filename != ""){?>
		        <img src="<?php echo $logo_filename;?>" style="max-height:300px;" />
            <?php } ?>
	        <div data-role="content">
                <ul data-role="listview" data-inset="true" data-theme="c">
	<li data-role="list-divider"><?php echo __("Please select a further action", "qr-code-waiter-calling-system") ?></li>
	<li>
		<a id="0" href="" data-transition="fade">
		<img src="../qr-code-waiter-calling-system/images/waitress.png" />
		<h2><?php echo __("Call Waiter", "qr-code-waiter-calling-system") ?></h2>
		</a>
	</li>
	<li>
		<a id="1" href="" data-transition="fade">
			<img src="../qr-code-waiter-calling-system/images/menu.png" />
			<h2><?php echo __("Request Menu", "qr-code-waiter-calling-system") ?></h2>
		</a>
	</li>
	<li>
		<a id="2" href="" data-transition="fade">
			<img src="../qr-code-waiter-calling-system/images/pay.png" />
			<h2><?php echo __("Request Bill", "qr-code-waiter-calling-system") ?></h2>
		</a>
	</li>
</ul>
            </div>
        </div>
        <div data-role="page" data-theme="b" id="success" style="text-align: center">
	        <div data-role="header">
		        <h1><?php echo $site_name;?></h1>
	        </div>
            <?php if($logo_filename != ""){?>
		        <img src="<?php echo $logo_filename;?>" style="max-height:300px;" />
            <?php } ?>
<div data-role="content">
                <div class="ui-body ui-body-b">
	<h2><?php echo __("Your action is taken", "qr-code-waiter-calling-system") ?></h2>
	<p><?php echo __("We will serve you as soon as possible.", "qr-code-waiter-calling-system") ?></p>
	<p><?php echo __("Thank you!", "qr-code-waiter-calling-system") ?></p>
</div>
            </div>
        </div>

        <div data-role="page" data-theme="b" id="error">
	        <div data-role="header">
		        <h1><?php echo $site_name;?></h1>
	        </div>
            <?php if($logo_filename != ""){?>
		        <img src="<?php echo $logo_filename;?>" style="width: 100%" />
            <?php } ?>
<div data-role="content">
                <div class="ui-body ui-body-b">
	<h2 style="color: red"><?php echo __("Your action is not taken", "qr-code-waiter-calling-system") ?></h2>
	<p><?php echo __("We are sorry but Waiter Calling System is now working.", "qr-code-waiter-calling-system") ?></p>
	<p><?php echo __("Please call waiter directly.", "qr-code-waiter-calling-system") ?></p>
</div>
            </div>
        </div>


        <script>

            $('a').on('click', function (e) {

            var action = { ActionType: this.id, Location: "<?php echo $_GET["location"] ?>" };
                var actionEncoded = (encodeURI(JSON.stringify(action)));			


						$.get( "<?php echo $api_call_url; ?>" + actionEncoded, function( data ) {
            
            
            var success = (data.trim()!='<?php echo $error_result;?>');
							if(success)
								$.mobile.changePage("#success");

							else
								$.mobile.changePage("#error");
								
						}).fail(function() {
								$.mobile.changePage("#error");
						});
            });

        </script>
        </body>
</html>

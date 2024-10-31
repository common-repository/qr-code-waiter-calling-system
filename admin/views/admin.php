<?php
/**QR Code Waiter Calling System.
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package QR Code Waiter Calling System
 * @author  Catkin <catkin@catzsoft.ee>
 * 
 */

 
 	//load variables
	$logo_filename           = QRCodeWaiterCallingSystem_Public::getLOGOfilename();
	$old_logo_local_filename = QRCodeWaiterCallingSystem_Public::getLOGOfilenameLocal();
	$api_key                 = QRCodeWaiterCallingSystem_Public::getAPIkey();
	$lang					 = QRCodeWaiterCallingSystem_Public::getLanguage();

	$message = '';
	
	if(isset($_REQUEST['action']))
		if($_REQUEST['action']=='save'){
			//moving uploaded file      
			if(isset($_FILES["file"])){
				
				//handle wordpress upload
				if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
				$uploadedfile = $_FILES['file'];
				$upload_overrides = array( 'test_form' => false );
				$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
				
				//upload success
				if ( $movefile ) {
				    $message = "File is valid, and was successfully uploaded.\n";
					$http_filename   = $movefile['url'];
					$local_file_name = $movefile['file'];
					
					
				//upload error
				} else {
				    $message = "File has failed to upload due to an error! Is its parent directory (wp-content/uploads/...) writable by the server?<br/>";
				}				
				
				QRCodeWaiterCallingSystem_Public::setLOGOfilename($http_filename, $local_file_name);
				
				$logo_filename = $http_filename;
				
				//message for user
				$message .= 'Settings are saved';
			}
			
		//api reset
		}else if($_REQUEST['action']=='reset_api_key'){
			QRCodeWaiterCallingSystem_Public::resetApiKey();
			$api_key = QRCodeWaiterCallingSystem_Public::getAPIkey();
			$message = 'Api was reset.';
		}
		
		
		
?>
<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
	<h3>Your Api key is: <?php echo $api_key;?></h3>	
	<?php
	  if(QRCodeWaiterCallingSystem_Public::apiKeyWrong($api_key))
	  	echo "Your api key wasn't generated properly. You can call api by clicking on button <button onclick=\"window.location='?page=qr-code-waiter-calling-system&action=reset_api_key';\">Get API key</button><br/><br/><br/>";
	?>

    <h3>Step 1: Create short links and QR codes.</h3>
Use links below to create short links and QR codes. You can use short code services like <a href="http://goo.gl" target="_blank">goo.gl</a> or <a href="http://bit.ly" target="_blank">bit.ly</a>
    <br/>
Each link contains number of your table. You can use any values here. 
    <br/>
    <br/>
    <b>For example:</b>
    <br/>
    <br/>
    Table 1:
    <br/>
	<a href="<?php echo site_url();?>/wp-content/plugins/qr-code-waiter-calling-system/?location=Table 1" target="_blank"><?php echo site_url();?>/wp-content/plugins/qr-code-waiter-calling-system/?location=Table 1</a>
	<br/>
    <br/>
    Table 2:
    <br/>
	<a href="<?php echo site_url();?>/wp-content/plugins/qr-code-waiter-calling-system/?location=Table 2" target="_blank"><?php echo site_url();?>/wp-content/plugins/qr-code-waiter-calling-system/?location=Table 2</a>
	<br/>
    <br/>
    Table 3:
    <br/>
	<a href="<?php echo site_url();?>/wp-content/plugins/qr-code-waiter-calling-system/?location=Table 3" target="_blank"><?php echo site_url();?>/wp-content/plugins/qr-code-waiter-calling-system/?location=Table 3</a>

<h3>Step 2: Print media</h3>
Print out QR codes and stick them on a table or insert into plastic stands

<h3>Step 3: Define logo</h3>
Define logo that will be displayed on mobile page

	<?php
		if($logo_filename!='') echo "<b>Current logo:</b><br/><img src=\"$logo_filename\" style=\"max-width:200px;max-height:200px;\"/>";

	?>
	<form method="post" enctype="multipart/form-data" action="<?php echo the_permalink(); ?>">
		<input type="hidden" name="action" value="save"/>
		New Logo: <input type="file" name="file"/>
		<button type="submit">Upload and Save</button>	
	</form>


<h3>Step 4: Test it</h3>
Scan different codes and try to select actions yourself

<h3>Step 5: View actions</h3>
Open following link on Tablet PC or on Mobile to see requested actions
    <br/>
    <a href="https://wpqrcodewaitercallingsystemqueue.azurewebsites.net/<?php echo $lang?>/<?php echo $api_key;?>" target="_blank">View actions</a>
	<?php

		//echo message from succes or error action
		if($message!='')
			echo "<br/><b>$message</b>";
	?>
</div>

<div style="margin-top:100px;">
		For any questions or extra features please contact: <a href="mailto:info@catzsoft.ee?subject=I'm interested in your QR Code Waiter Callin System plugin">info@catzsoft.ee</a> or <a href="https://wa.me/3725165285?text=I'm%20interested%20in%20your%20QR%20code%20waiter%20calling%20plugin">Whatsapp</a>
		<p><span style="color:green;font-weight: bold;">Try our </span><a href="https://reservationdiary.eu/">ReDi Restaurant Reservation plugin</a>
		<p><span style="color:green;font-weight: bold;">Try our </span><a href="http://u-serve.me/eng/dm1">Digital menu for restaurants</a>
</div>
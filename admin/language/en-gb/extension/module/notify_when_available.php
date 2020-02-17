<?php
// Heading
$_['heading_title']    = "<b>notify when available</b>";
$_['text_extension']   = 'Extensions';
$_['text_module']      = 'Modules';
$_['text_success']     = 'Success: You have modified notify when available module!';
$_['text_edit']        = 'Edit Notify When Available Module';
$_['text_unregister']  	= 'Guest';
$_['text_register']  		= 'Register';
$_['text_show_customer'] 	= 'View Detail';
$_['text_store_name'] 		= "Store: %s";

// Entry
$_['entry_status']     		= 'Status';
$_['entry_out_of_stock']  	= 'Show Out Of Stock Label';
$_['entry_notify_button']  	= 'Show Notify Button';
$_['entry_email_subject'] 	= 'Email Subject:';
$_['entry_email_body'] 		= 'Email Body:';

$_['tab_notify_product']    = 'Notify Products';
$_['tab_email']     		= 'Email';
$_['column_customer']  		= 'Customer';
$_['column_product']  		= 'Product';
$_['column_message']  		= 'Message';
$_['column_date_added']  	= 'Date';

// Error
$_['error_permission'] 		= 'Warning: You do not have permission to modify notify when available module!';
$_['error_email_subject'] 	= 'You can not leave this blank.';
$_['error_email_body'] 		= 'You can not leave this blank.';

// email title
$_['heading_email_to_customer']  	= 'For Customer';
$_['heading_email_to_admin']  		= 'For Admin';

// help
$_['help_email_subject'] 	= "Email subject for email.";
$_['help_email_body'] 		= "Email content for email. Please use the below codes for dynamic value";

$_['email_subject'] 		= "Product is back on stock";
$_['email_body'] 			= "<table style='width:100%'>  <tbody><tr> <td align='center'> <table style='width:100%;margin:0 auto;border:1px solid #f0f0f0;padding:10px;line-height:1.8'>  <tbody><tr> <td> <p>Hello <strong>{firstname} {lastname}</strong>,</p><p>The product {product_name} is back in stock. Please follow the url {product_url} and buy.</p><p>Kind Regards,</p><p>YourStore<br> <a href='http://www.example.com' target='_blank'>http://www.example.com</a></p> </td></tr>  </tbody> </table> </td></tr>  </tbody> </table>";
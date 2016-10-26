<?php

/* === NEWSLETTER SETTINGS === */
// Only enable one method at one time for best results

$use_mysql = true;
$use_mailchimp = false;



/* === CONTACT SETTINGS === */

$email_messages = true;
$store_messages_mysql = true;



/* === MAILCHIMP SETTINGS === */
// Retrieve these from your MailChimp dashboard

$list_id = 'YOUR_LIST_ID';
$api_key = 'YOUR_API_KEY';



/* === MYSQL SETTINGS & MAIL SETTINGS === */
/* These settings are required when using MySQL, as the processor for the newsletter and when storing contact form requests */ 

// Host name, domain or IP Address for the MySQL server's location
$mysql_server = "localhost";

// Username used to connect to the MySQL server
$mysql_user = "root";

// Password used to connect to the MySQL server
$mysql_password = "root";

// Name of the MySQL database to connect to
$mysql_db = "my_database";

// Name of the MySQL table to hold the email addresses
$mysql_table_subscriptions = "signups";

// Name of the MySQL table to hold the email addresses
$mysql_table_contact = "contact";

// Email address to send alerts
$your_email_address = "youremailaddress@domain.com";

// Array of MySQL details (DO NOT edit this)

$mysql_settings = array($mysql_server, $mysql_user, $mysql_password, $mysql_db, $your_email_address);



/* === ERROR MESSAGES === */

// Message to display when no email address is entered
$emptyemail_error = "<i class='fa fa-exclamation-triangle'></i>Please enter an email address!";

// Message to display when the entered email address is invalid
$invalidemail_error = "<i class='fa fa-exclamation-triangle'></i>Please enter a valid email address!";

// Message to display when the email address was not inserted into the database successfully
$insertemail_error = "<i class='fa fa-exclamation-triangle'></i>Sorry, we had an error!";

// Message to display when no name is entered
$emptyname_error = "<i class='fa fa-exclamation-triangle'></i>Please enter a name";

// Message to display when no message is entered
$emptymsg_error = "<i class='fa fa-exclamation-triangle'></i>Your message has no content";

// Message to display when the user is successfully signed up
$success_msg = "<i class='fa fa-check-circle' style='color: #40d47e;''></i>Signup successful!";

// Message to display when contact form message is sent successfully
$msg_sent = "<i class='fa fa-check-circle' style='color: #40d47e;''></i>Your message has been sent";

// Message to display for clashing email addresses
$email_exists_error = "<i class='fa fa-exclamation-triangle'></i>Email address already subscribed!";

// Unknown error message
$unknown_error = "<i class='fa fa-exclamation-triangle'></i>An error occured!";

// Array of messages (DO NOT edit this)
$error_messages = array($emptyemail_error, $invalidemail_error, $insertemail_error, $success_msg, $email_exists_error, $unknown_error, $emptyname_error, $emptymsg_error, $msg_sent);

?>
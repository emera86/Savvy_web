<?php

/* This code shouldn't require any editing, all variables can be set in the variables.php file. */

require_once ('MCAPI.class.php');
include 'variables.php';

$full_name = mysql_real_escape_string($_POST['fullname']);
$user_email = mysql_real_escape_string($_POST['signup-email']);
$contact_email = mysql_real_escape_string($_POST['emailaddress']);
$message = mysql_real_escape_string($_POST['message']);

/* === MAILCHIMP === */

function mailChimp($email, $api, $list_id, $messages) {

  // Retrieve API key from: http://admin.mailchimp.com/account/api/
  $api = new MCAPI($api);
  
  if($api->listSubscribe($list_id, $email, '') === true) {
    // Success! 
    $status = 'success';
    $message = $messages[3];
  } else if (empty($email)) {  
      $status = "error";  
      $message = $messages[0]; 
  } else if (!preg_match('/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/', $email)) {
    $status = "error";  
    $message = $messages[1];  
  } else {
    // An error ocurred, return error message 
    $status = 'error';
    $message= $messages[4];
  }
  
  $data = array(  
    'status' => $status,  
    'message' => $message  
  );  
  
  echo json_encode($data);   
  exit; 
}

/* === MYSQL SUBSCRIBE === */

function mysqlSubscribe($mysql_settings, $mysql_table, $email, $messages) {


  if(empty($email)){  
    $status = "error";  
    $message = $messages[0]; 
  } else if (!preg_match('/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/', $email)) {
    $status = "error";  
    $message = $messages[1];  
  } else if($check > 0) {
    $status = "error";
    $message = $messages[4];
  } else {  
    $date = date('Y-m-d');  
    $time = date('H:i:s');

    $connect = mysql_connect($mysql_settings[0],$mysql_settings[1],$mysql_settings[2]);    
    mysql_select_db($mysql_settings[3]); 
    
    $collision = mysql_query("SELECT signup_email_address FROM $mysql_table WHERE signup_email_address='$email'");

    $if_collision = mysql_num_rows($collision);

    if($if_collision == 0) {
        
        $insertSignup = mysql_query("INSERT INTO $mysql_table (signup_email_address, signup_date, signup_time) VALUES ('$email', '$date', '$time')");

        // Send subscription alert
        $subscribe_message = "A new user with the email: " . $email . " subscribed to your newsletter."; 
        mail($your_email_address, "Newsletter Subscription Alert", $subscribe_message);
       
        if($insertSignup){  
            $status = "success";  
            $message = $messages[3];     
        }  else {  
          $status = "error";  
          $message = $insertmsg_error;    
        } 
    } else {
      $status = "error";  
      $message = $messages[4];
    }
  }    
  
  //return json response   
  $data = array(  
    'status' => $status,  
    'message' => $message  
  );  
  
  echo json_encode($data);  

  exit;  
}

/* === MYSQL CONTACT === */

function contact($mysql_settings, $mysql_table, $name, $email, $message_to_send, $messages) {
   
  $connect = mysql_connect($mysql_settings[0],$mysql_settings[1],$mysql_settings[2]);    
  mysql_select_db($mysql_settings[3]); 

  if(empty($name)){  
    $status = "error";  
    $message = $messages[6]; 
  } else if (empty($email)) {
    $status = "error";  
    $message = $messages[0]; 
  } else if (!preg_match('/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/', $email)) {
    $status = "error";  
    $message = $messages[1];  
  } else if(empty($message_to_send)) {
    $status = "error";
    $message = $messages[7];
  } else {
    $insertSignup = mysql_query("INSERT INTO $mysql_table (name, email, message) VALUES ('$name', '$email', '$message_to_send')");

        // Send subscription alert
        $contact_message = "Contact request from: " . $name . "\n\n" . $message_to_send; 
        mail($your_email_address, "Contact Request", $contact_message);
       
        if($insertSignup){  
            $status = "success";  
            $message = $messages[8];     
        }  else {  
          $status = "error";  
          $message = $insertmsg_error;    
        } 
}  
  
  //return json response   
  $data = array(  
    'status' => $status,  
    'message' => $message  
  );  
  
  echo json_encode($data);  

  exit;
}

// Handle forms

if ($_GET['action'] == 'signup' && $use_mailchimp == true) {
  mailChimp($user_email, $api_key, $list_id, $error_messages);
} else if ($_GET['action'] == 'signup' && $use_mysql == true) {
  mysqlSubscribe($mysql_settings, $mysql_table_subscriptions, $user_email, $error_messages);
} else if ($_GET['action'] == 'contact') {
  contact($mysql_settings, $mysql_table_contact, $full_name, $contact_email, $message, $error_messages);
}

?>
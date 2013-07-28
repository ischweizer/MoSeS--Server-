<?php
session_start();

include_once("./include/functions/func.php");

/**
* Get survey's predefined questions
*/ 

if(isset($_SESSION['USER_LOGGED_IN']) && 
    isset($_POST['get_questions_pwd']) && $_POST['get_questions_pwd'] == 6767 &&
    isset($_POST['get_questions']) && is_numeric($_POST['get_questions'])){

    include_once("./include/providers/_study-add-survey-get-questions-provider.php");
}

/*
* Update pending requests to be a scientist 
*/

if(isset($_SESSION['USER_LOGGED_IN']) && 
    isset($_REQUEST['hash']) && !empty($_REQUEST['hash']) && is_md5($_REQUEST['hash'])){
    
    if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
       
        include_once("./include/providers/_approve-scientist-provider.php");
   }
}

/*
* Handling user CREATES or JOINS existing group 
*/

if(isset($_SESSION['USER_LOGGED_IN']) && isset($_SESSION['USER_ID']) &&
    isset($_REQUEST['group_name']) && !empty($_REQUEST['group_name']) &&
    isset($_REQUEST['group_password']) && !empty($_REQUEST['group_password']) &&
   (isset($_REQUEST['createGroup']) && $_REQUEST['createGroup'] == $_SESSION['USER_ID'] || 
    isset($_REQUEST['joinGroup']) && $_REQUEST['joinGroup'] == $_SESSION['USER_ID']) &&
    !(isset($_REQUEST['createGroup']) && isset($_REQUEST['joinGroup']))){
     
     include_once("./include/providers/_create-join-group-provider.php");   
}
 
/*
* Handling user LEAVES his group 
*/

if(isset($_SESSION['USER_LOGGED_IN']) && isset($_SESSION['USER_ID']) &&
   isset($_REQUEST['leaveGroup']) && !empty($_REQUEST['leaveGroup']) &&
   intval($_REQUEST['leaveGroup']) == $_SESSION['USER_ID']){
      
     include_once("./include/providers/_leave-group-provider.php");   
}

/*
* Handling of json request for new devices table
*/
                                                                 
if(isset($_SESSION['USER_LOGGED_IN']) && 
   isset($_REQUEST['pages']) && !empty($_REQUEST['pages']) && 
   isset($_REQUEST['pageMax']) && !empty($_REQUEST['pageMax']) &&
   isset($_REQUEST['curPage']) && !empty($_REQUEST['curPage'])){
    //configuration of connection
    
    include_once("./include/providers/_devices-request-pager-provider.php");  
}

/*
* Handle user LOGIN
*/
if(isset($_POST["submit"]) && $_POST["submit"] == "1"){
	
    include_once("./include/providers/_login-provider.php");
}

/**
 * Handling of requests for checking the uniqueness of an email
 * a new user is trying to register with.
 * The new user has to provide a new email.
 */
if(isset($_POST['isEmailUnique']) && !empty($_POST['isEmailUnique'])){

    include_once("./include/providers/_email-unique-check-provider.php");
}

/**
 * Checking the uniqueniness of the provided form on registration and 
 * sending the email to the user if it is.
 * This functions echoes back:
 * 		0 if the email was unique and confirmation email has been sent
 * 		1 if the email was not unique
 * 		2 if there has been a problem sending the email (i.e. the mail server did not respond)
 */
if(isset($_POST["submitted"]) && $_POST["submitted"] == "1"){
    
	include_once("./include/providers/_registration-provider.php");
}

/**
 * Checking the correctness of the email provided form on forgot password form and
 * sending the email to the user if it is.
 * This functions echoes back:
 * 		0 if the provided email is known by the server and the email has been sent
 * 		1 if the email provided by the user is unknown to the server
 * 		2 the server knows the email, but there has been a problem sending the email (i.e. the mail server did not respond)
 */
if(isset($_POST["submitted_forgot"]) && $_POST["submitted_forgot"] == "1"){
	
    include_once("./include/providers/_reset-password-provider.php");
}

/**
 * Changes the password. The user is identified by the provided email-hash
 * This functions echoes back:
 * 		0 if the password is successfully changed
 */
if(isset($_POST["hash"]) && isset($_POST["newPassword"])){
	
   include_once("./include/providers/_reset-password-confirmation-provider.php"); 
}

/*
*  UPLOAD Handling. User study CREATE
*/
if(isset($_SESSION['USER_LOGGED_IN']) &&
   isset($_REQUEST['study_create']) && !empty($_REQUEST['study_create']) && $_REQUEST['study_create'] == 2975){
    
   include_once("./include/providers/_study-create-upload-provider.php");
}

if(isset($_SESSION['USER_LOGGED_IN']) && 
   isset($_REQUEST['study_update']) && !empty($_REQUEST['study_update']) && $_REQUEST['study_update'] == 6825){
       
   include_once("./include/providers/_study-update-upload-provider.php"); 
}
?>
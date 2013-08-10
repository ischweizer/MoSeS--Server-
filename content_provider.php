<?php
session_start();

include_once("./include/functions/func.php");

/*
*   Apply as scientist
*/

if(isset($_SESSION['USER_LOGGED_IN']) && 
    isset($_POST['promo_sent']) && $_POST['promo_sent'] == 9325 &&
    isset($_POST['reason']) && !empty($_POST['reason'])){

    include_once("./include/providers/_apply-as-scientist-provider.php");
    exit;
}

/*
*   Reject scientist by admin
*/

if(isset($_SESSION['USER_LOGGED_IN']) &&
    isset($_POST['reject']) && $_POST['reject'] == 3434 &&
    isset($_REQUEST['hash']) && !empty($_REQUEST['hash']) && is_md5($_REQUEST['hash'])){
    
    if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){

        include_once("./include/providers/_reject-scientist-provider.php");
        exit;
    }
}

/**
*  Survey remove from user study
*/ 

if(isset($_SESSION['USER_LOGGED_IN']) && 
    isset($_POST['study_survey_remove_code']) && $_POST['study_survey_remove_code'] == 4931 &&
    isset($_POST['study_survey_remove']) && is_numeric($_POST['study_survey_remove'])){

    include_once("./include/providers/_study-remove-survey-provider.php");
    exit;
}

/**
* Get survey's predefined questions
*/ 

if(isset($_SESSION['USER_LOGGED_IN']) && 
    isset($_POST['get_questions_pwd']) && $_POST['get_questions_pwd'] == 6767 &&
    isset($_POST['get_questions']) && is_numeric($_POST['get_questions'])){

    include_once("./include/providers/_study-add-survey-get-questions-provider.php");
    exit;
}

/**
* Remove study
*/ 

if(isset($_SESSION['USER_LOGGED_IN']) && 
    isset($_POST['study_remove']) && !empty($_POST['study_remove'])){

    include_once("./include/providers/_study-remove-provider.php");
    exit;
}

/*
* Approve a scientist 
*/

if(isset($_SESSION['USER_LOGGED_IN']) && 
    isset($_POST['allow']) && $_POST['allow'] == 4343 &&
    isset($_REQUEST['hash']) && !empty($_REQUEST['hash']) && is_md5($_REQUEST['hash'])){
    
    if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
       
        include_once("./include/providers/_approve-scientist-provider.php");
        exit;
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
     exit;
}
 
/*
* Handling user LEAVES his group 
*/

if(isset($_SESSION['USER_LOGGED_IN']) && isset($_SESSION['USER_ID']) &&
   isset($_REQUEST['leaveGroup']) && !empty($_REQUEST['leaveGroup']) &&
   intval($_REQUEST['leaveGroup']) == $_SESSION['USER_ID']){
      
     include_once("./include/providers/_leave-group-provider.php");   
     exit;
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
    exit;
}

/*
* Handle user LOGIN
*/
if(isset($_POST["submit"]) && $_POST["submit"] == "1"){
	
    include_once("./include/providers/_login-provider.php");
    exit;
}

/**
 * Handling of requests for checking the uniqueness of an email
 * a new user is trying to register with.
 * The new user has to provide a new email.
 */
if(isset($_POST['isEmailUnique']) && !empty($_POST['isEmailUnique'])){

    include_once("./include/providers/_email-unique-check-provider.php");
    exit;
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
    exit;
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
    exit;
}

/**
 * Changes the password. The user is identified by the provided email-hash
 * This functions echoes back:
 * 		0 if the password is successfully changed
 */
if(isset($_POST["hash"]) && isset($_POST["newPassword"])){
	
   include_once("./include/providers/_reset-password-confirmation-provider.php"); 
   exit;
}

/*
*  UPLOAD Handling. User study CREATE
*/
if(isset($_SESSION['USER_LOGGED_IN']) &&
   isset($_REQUEST['study_create']) && !empty($_REQUEST['study_create']) && $_REQUEST['study_create'] == 2975){
    
   include_once("./include/providers/_study-create-upload-provider.php");
   exit;
}

/* 
*  UPLOAD Handling. User study UPDATE 
*/
if(isset($_SESSION['USER_LOGGED_IN']) && 
   isset($_REQUEST['study_update']) && !empty($_REQUEST['study_update']) && $_REQUEST['study_update'] == 6825){
       
   include_once("./include/providers/_study-update-upload-provider.php"); 
   exit;
}
?>
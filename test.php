<?php
include_once("./config.php");
include_once("./include/functions/dbconnect.php");
include_once("./include/functions/logger.php");

// Replace with real BROWSER API key from Google APIs
$apiKey = $CONFIG['GPUSH']['GOOGLE_SERVER_KEY'];

// Replace with real client registration IDs
$registrationIDs = array( "APA91bGKFNXXLmU2s93TRjXVsDqq6LQPn0OQyYtYB37MVGdf1mhoc_3X8aq1TBrgEg4qYDTYw5f155s4VhA6ETmcVhXF2iRBTAevl69iS5wWvXc_AAET7YeKBfgv72yCvqafrzYR_tEwHN0_x1CELKK1bGwX1ZGOFE8n8KWVBpRT2vxeWJa640Q");

// Message to be sent
$message = json_encode(array(
        		'MESSAGE' => "USERSTUDY",
        		'APKID' => "362"));

// Set POST variables
$url = 'https://android.googleapis.com/gcm/send';

$fields = array(
		'registration_ids'  => $registrationIDs,
		'data'              => array( "message" => $message ),
);

$headers = array(
		'Authorization: key=' . $apiKey,
		'Content-Type: application/json'
);

// Open connection
$ch = curl_init();

// Set the url, number of POST vars, POST data
curl_setopt( $ch, CURLOPT_URL, $url );

curl_setopt( $ch, CURLOPT_POST, true );
curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

// Execute post
$result = curl_exec($ch);

// Close connection
curl_close($ch);

echo $result;

?>

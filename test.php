<?php
include_once("./config.php");
include_once("./include/functions/dbconnect.php");
include_once("./include/functions/logger.php");

// Replace with real BROWSER API key from Google APIs
$apiKey = $CONFIG['GPUSH']['GOOGLE_SERVER_KEY'];

// Replace with real client registration IDs
$registrationIDs = array( "APA91bE1y0CIHeKgEz3IpOPc7dtmi4cXLqS6Wq8c5aIl_9-T-tRgPTIJ0BgjeTXEAee15u2vPjMSiRH1jwKyBef-TeBJkhlQz29lP-k7s9JAUI7BKixknOrtXcOyVIwW1Phuije0aNQX3svKL2zi9tK3hwRmQnZGV7hev9RsRoKT788dF7pVJDk");

// Message to be sent
$message = "H A A L O  G U T E N  T A G";

// Set POST variables
$url = 'https://android.googleapis.com/gcm/send';

$fields = array(
		'registration_ids'  => $registrationIDs,
		'data'              => array( "message" => $message ),
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

<?php
/**
* This class manages the creation and dropping of tables
*/
class GooglePushManager
{
    /**
    * Sends apkid of the new apk to all targetDevices
    * 
    * @param String $apkid  the id for the user study
    * @param String $targetDevices array of String ids of target devices c2dm-ids
    * @param logger the Logger
    */
    public static function googlePushSend($apkid, $targetDevices, $logger){
        
        //$logger->logInfo("GOOGLE PUSH MANAGER TARGET DEVICES");
        //$logger->logInfo(print_r($targetDevices, true));
    
        // LOGIN AT GOOGLE AUTHENTIFICATION SERVER
        $account = "moses.tud@googlemail.com"; // Account
        $pass = "mittwoch"; // Password
        $src = "moses service"; // Project Name

        $post_params = array("Email" => $account, "Passwd" => $pass, "accountType" => "HOSTED_OR_GOOGLE", "source" => $src, "service" => "ac2dm");
        $req = curl_init("https://www.google.com/accounts/ClientLogin");
        curl_setopt($req, CURLOPT_HEADER, 1);
        curl_setopt($req, CURLOPT_POST, 1);
        curl_setopt($req, CURLOPT_POSTFIELDS, $post_params);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($req);
        
        
        //$logger->logInfo("ANSWER FROM GOOGLE REGARDING AUTHENTIFICATION: ");
       // $logger->logInfo(print_r($data, true));
        
        
        curl_close($req);
                        
        $data = trim($data);
        
        // parsing auth key
        $authKey = substr($data, strrpos($data, "Auth=")+5, strlen($data)-2);
        

        // SENDING THE MESSAGE TO ALL IDs
        foreach($targetDevices as $GOOGLE_C2DM_ID){
        
            $device_id = "1"; 

            $headers = array('Authorization: GoogleLogin auth=' . $authKey);
            $data = array('registration_id' => $GOOGLE_C2DM_ID, 'collapse_key' => 'ck_' . $device_id, 'data.MESSAGE' => "USERSTUDY", 'data.APKID' => $apkid);

            $req = curl_init();
            curl_setopt($req, CURLOPT_URL, "https://android.apis.google.com/c2dm/send");
            curl_setopt($req, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($req, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($req, CURLOPT_POST, true);
            curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($req, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($req);
            
           // $logger->logInfo("ANSWER FROM GOOGLE REGARDING PUSH: ");
            //$logger->logInfo(print_r($response, true));
        
        }
      
    }
      

    
    



 
}
?>
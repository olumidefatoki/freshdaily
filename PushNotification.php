<?php 
class SendNotification {    
    private static $API_SERVER_KEY = 'AIzaSyCXcY8NaMrg72UYJDbEzb3Y6NH0Q5xKPRA';
    private static $is_background = "TRUE";
    public function __construct() {     
     
    }


    public function showme($token){
        echo $token;
    }
    public function sendPushNotificationToFCMSever($token, $message) {
        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
 
        $fields = array(
            'to' => $token,
            'notification' => array('title' => 'FreshDaily', 'body' =>  $message),
        );
        $headers = array(
            'Authorization:key=' . self::$API_SERVER_KEY,
            'Content-Type:application/json'
        );  
         
        // Open connection  
        $ch = curl_init(); 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post   
        $result = curl_exec($ch); 
        // Close connection      
        curl_close($ch);
        return $result;
    }
 }
?>
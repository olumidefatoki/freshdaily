<?php 

include_once 'PushNotification.php';
$serverObject = new SendNotification(); 

//$token = 'e5LXqmK2OoE:APA91bG_cwgteEqO7__2WAj3Mjbe4EKVARO2ZNNdXdQFdmafyrZSJQ6aWt9IDktZEQSSN58p97i2YrdsEKO2Vx_DZTQCLIDIFFn5II2lYVinnNIJUvwyCVQqVlCq5XKEN_eW90Np27AP';
$token = 'chMubYH9d1w:APA91bGFRvlydiFkgVbMsZs17MINZ8NPCpI-LGF6_EG_fYJMjOxJHdzfNThJ54xfLt3JfIoEYuYr4XGRxFiIEp4HOUs9HufWdsg_6OVE9wExYFiqcdIk8ODLdbODrpl3OwoplRhVUjKs';
$message = 'New Order From Ajima Farms Two';
$jsonString = $serverObject->sendPushNotificationToFCMSever($token, $message);  
echo $jsonString;


//IMPLEMENTATION NOTE:::
//1.//when marketer places order get  farmid and query not_id 
//call this function  sendPushNotificationToFCMSever($token, $message);
//not_id as token and message would be "New Order Came"


//2.//when farmer reject or accept order query marketer table for not_id 
//and send "You order was accepted or rejected"

?>
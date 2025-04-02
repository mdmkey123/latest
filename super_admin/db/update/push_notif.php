<?php
include '../config.php';

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json');
     header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
     
     if ($_SERVER['REQUEST_METHOD'] =='POST'){
         $title= $_POST['title'];
         $body= $_POST['body'];
         $deviceId= $_POST['device_id'];
        
         
         if(isset($_POST['message'])){
             $message= $_POST['message'];
         }else{
             $message="";
         }
         
         mysqli_query($conn,"UPDATE devices SET latitude='NA', longitude='NA' WHERE id='$deviceId'");
         $query= mysqli_query($conn,"SELECT * FROM devices WHERE id='$deviceId'");
         if(mysqli_num_rows($query)>0){
             $device= $query->fetch_assoc();
             $result= sendNotification($title,$body,$message,$device['fcm_token']);
             if($result){
                 if(isset($_POST['indexes'])){
                     $indexes= $_POST['indexes'];
                     mysqli_query($conn,"UPDATE device_controls SET feature_indexes ='$indexes' WHERE device_id='$deviceId'");
                     $deviceControls= mysqli_query($conn,"SELECT * FROM device_controls WHERE device_id='$deviceId'")->fetch_assoc();
                     $indexes2= $deviceControls['feature_indexes'];
                 }
                 echo json_encode(["status"=>"success","message"=>"Push notification sent successfully","feature_indexes"=>$indexes2]);
             }
         }else{
             echo json_encode(["status"=>"error","message"=>"failed in sending push notification, Pls contact your technical support."]);
         }
         
         
     }else{
         echo json_encode(array('message' => 'Invalid request method', 'status' => "error"));
     }
     function sendNotification($title,$body,$message,$deviceToken){
        //  $url = 'https://fcm.googleapis.com/fcm/send';
         
                 $accessToken = getAccessToken();
            if (!$accessToken) {
                die('Error getting access token');
            }
        
            $firebaseProjectId = 'emicare-15d32'; // Replace with your Firebase project ID
            $url = "https://fcm.googleapis.com/v1/projects/$firebaseProjectId/messages:send";
            /*$notification = [
                    'title' => $title,
                    'body'  => $body
                ];
            
                $fields = [
                    'to'   => $deviceToken,
                    'data' => $notification,
                    'content_available'=> true,
                    'priority'=> 'high'
                ];
            
                $headers = [
                    'Authorization: key=' . $accessToken,
                    'Content-Type: application/json'
                ];
            
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));*/
        
            $data = [
                'message' => [
                    'token' => $deviceToken,
                    'data' => [ // Use "data" instead of "notification"
                        'title' => $title,
                        'body' => $body,
                        'message'=> $message
                    ],
                    'android' => [
                        'priority' => 'high'
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-priority' => '10'
                        ]
                    ]
                ]
            ];
        
            $headers = [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ];
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
            $result = curl_exec($ch);
            curl_close($ch);
            return true;
     }
     function getAccessToken() {
            $jsonKeyFile = 'https://mdmlock.in/jmvpay/apis/uploads/ZyntroFirebasePrivateKey.json'; // Path to your service account JSON file
            $tokenUri = 'https://oauth2.googleapis.com/token';
        
            $keyData = json_decode(file_get_contents($jsonKeyFile), true);
            $now = time();
            $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $jwtPayload = base64_encode(json_encode([
                'iss' => $keyData['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $tokenUri,
                'exp' => $now + 3600,
                'iat' => $now
            ]));
        
            $unsignedJwt = $jwtHeader . '.' . $jwtPayload;
            openssl_sign($unsignedJwt, $signature, $keyData['private_key'], 'sha256WithRSAEncryption');
            $jwt = $unsignedJwt . '.' . base64_encode($signature);
        
            $postData = [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ];
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $tokenUri);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            $result = json_decode(curl_exec($ch), true);
            curl_close($ch);
        
            return $result['access_token'] ?? null;
        }
?>
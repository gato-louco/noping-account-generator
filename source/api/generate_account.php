<?php

 ##### PART 1 ######

$ch = curl_init();


curl_setopt_array($ch, [


      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_FOLLOWLOCATION => true,


      CURLOPT_HTTPHEADER => [
           'User-Agent: abc'
      ],


      CURLOPT_URL => 'https://www.noping.com/pt-br/auth/login',


      CURLOPT_RETURNTRANSFER => true

]);

curl_setopt($ch, CURLOPT_HEADER, true);

$result = curl_exec($ch);

curl_close($ch);




// extracting token xsrf
$pos_1 =  stripos($result, "XSRF-TOKEN=" ) + 11;
if($pos_1 <= 11){ die("stripos XSRF-TOKEN error");}
$xsrf_token = (substr($result, $pos_1, stripos($result, ";", $pos_1) - $pos_1));

//extracting token session
$pos_1 =  stripos($result, "nptunnel_session=" ) + 17;
if($pos_1 <= 17){ die("stripos nptunnel_session error");}
$np_tunnel_token = (substr($result, $pos_1, stripos($result, ";", $pos_1) - $pos_1));

// extracting _token
$pos_1 =  stripos($result, '_token" value="') + 15;
if($pos_1 <= 15){ die("stripos _token error");}
$_token = (substr($result, $pos_1, stripos($result, "\"", $pos_1) - $pos_1));




###### PART 2 ########

// generate password, email and hwid (all the same)

$alphabet = "abcdefghijklmnopqrstuvwxyz";
$email = "";
for ($x=0;$x<12;$x++){
    $email .= $alphabet[rand(0,24)];
}
$password = $email;
$hwid = $password;
$email .= "@gmail.com";
$_email = urlencode($email);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://noping.com/en/auth/signup',
  CURLOPT_RETURNTRANSFER => true,
  //CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => "_token=$_token&nome=By%20GatoLouco&usuario=$_email&senha=$password&csenha=$password&agree=on&phone=21321323121123213&fp=$password",
  CURLOPT_HTTPHEADER => [
    'User-Agent: firefox',
    'Content-Type: application/x-www-form-urlencoded',
    "Cookie: XSRF-TOKEN=$xsrf_token; nptunnel_session=$np_tunnel_token"
  ],
));
curl_setopt($curl, CURLOPT_HEADER, true);
 $response = curl_exec($curl);

curl_close($curl);
###### PARTE 3 ########

// Activating trial account
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://ley8.scutumnet.com/api/nptunnel_api_auth/public/trial',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => "{\"machine_id\":\"$hwid\",\"password\":\"$password\",\"software_id\":1,\"user\":\"$email\"}",
  CURLOPT_HTTPHEADER => [
    'User-Agent: abc',
    'Content-Type: application/json'
  ],
));
$response = curl_exec($curl);

curl_close($curl);
header("Content-type: application/json");

$obj1["email"] = $email;
$obj1["password"] = $password;
$obj2 = json_decode($response);
$obj_merged = (object) array_merge((array) $obj1, (array) $obj2);

echo(json_encode($obj_merged));

?>

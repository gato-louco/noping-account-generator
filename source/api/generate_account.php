<?php

 ##### PART 1 ######

$ch = curl_init();


curl_setopt_array($ch, [


      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,


      CURLOPT_HTTPHEADER => [
           'User-Agent: abc'
      ],


      CURLOPT_URL => 'https://www.nptunnel.com/pt-br/auth/login',


      CURLOPT_RETURNTRANSFER => true

]);

curl_setopt($ch, CURLOPT_HEADER, true);

$result = curl_exec($ch);

curl_close($ch);


// extracting token xsrf
$pos_1 =  stripos($result, "XSRF-TOKEN=" ) + 11;
$xsrf_token = (substr($result, $pos_1, stripos($result, ";", $pos_1) - $pos_1));

//extracting token session
$pos_1 =  stripos($result, "nptunnel_session=" ) + 17;
$np_tunnel_token = (substr($result, $pos_1, stripos($result, ";", $pos_1) - $pos_1));

// extracting _token
$pos_1 =  stripos($result, "_token\" value=\"") + 15;
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

// new request creating account
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://www.nptunnel.com/en/auth/signup',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => "_token=$_token&nome=By%20GatoLouco&usuario=$email&senha=$password&csenha=$password&agree=on&phone=13121232132&fp=427b4168a24b3535e6f2fa1ae2906411",
  CURLOPT_HTTPHEADER => [
    'User-Agent: abc',
    'Content-Type: application/x-www-form-urlencoded',
    "Cookie: XSRF-TOKEN=$xsrf_token; nptunnel_session=$np_tunnel_token"
  ],
));

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
// echo $response;
echo "{\"machine_id\":\"$hwid\",\"password\":\"$password\",\"software_id\":1,\"user\":\"$email\"}";
?>
<?php
session_start();

//step 2, a user session is started if not aleady there
if (!isset($_SESSION['usertoken'])) {
    //this isn't the best, but for demo purposes it's sufficient
    $_SESSION['usertoken'] = mt_rand();
}

function doCurl($endpoint, $payload = array())
{
    $payload['api_key'] = 'xxx'; //required, get from the client portal - never expose this key
    $json = json_encode($payload);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

    //dev only, selfsigned cert
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    return json_decode(curl_exec($ch), true);
}

//step 2, this function asks coinbee if the uesr was found give me payment info
function retrieveAddressForToken()
{
    $usertoken = $_SESSION['usertoken'];

    $payload = array(
        'identifier' => $usertoken, //required
    );

    //this will return a jsonencoded array with a payment information about the user if found
    $endpoint = 'https://api.coinbee.io/retrieve/address/identifier';

    return doCurl($endpoint, $payload);
}

//step 6, our content providing server makes a request to coinbee which will return an address and payment info
function retrieveAddress()
{
    $usertoken = $_SESSION['usertoken'];

    //here is where you can override what the default values were set for your site. If you have a piece of content that
    //should be priced differently, you can set that value here
    $payload = array(
//        'required_confirmations_override' => 3, //optional, default 0
//        'amount_override' => 1, //optional
//        'denomination_override' => 'usd' //optional
    );

    //this will return a jsonencoded array with an address, auto created if not found, and payment info if found
    $endpoint = 'https://api.coinbee.io/retrieve/' . $usertoken;

    return json_encode(doCurl($endpoint, $payload));
}

if (isset($_GET['proc']))
{
    header('Content-Type: application/json');
    echo retrieveAddress();
}

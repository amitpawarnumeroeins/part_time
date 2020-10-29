<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

// Include the bundled autoload from the Twilio PHP Helper Library
require 'src/Twilio/autoload.php';
use Twilio\Rest\Client;
// Your Account SID and Auth Token from twilio.com/console
$account_sid = 'AC58368fe26d4cd6ddade89ba79cb227e4';
$auth_token = 'd5a9a77889c39db2f309b635c50e6704';
// In production, these should be environment variables. E.g.:
// $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]
// A Twilio number you own with SMS capabilities
$twilio_number = "+15597154186";
$client = new Client($account_sid, $auth_token);
$client->messages->create(
// Where to send a text message (your cell phone?)
    '+919584092141',
    array(
        'from' => $twilio_number,
        'body' => 'I sent this message in under 10 minutes!'
    )
);

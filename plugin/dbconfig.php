<?php
require __DIR__.'/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


// This assumes that you have placed the Firebase credentials in the same directory
// as this PHP file.
$serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/parttime-c858c-firebase-adminsdk-yhr95-43f2777ee8.json');

$database = (new Factory)->withServiceAccount(__DIR__ . '/parttime-c858c-firebase-adminsdk-yhr95-43f2777ee8.json')
        ->createDatabase();

?>


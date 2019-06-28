<?php
/**
 * This serves as an example of how to use the Google API PHP Client
 * with Firebase Cloud Messaging Service.
 *
 * The client can be found here:
 * https://github.com/google/google-api-php-client
 *
 * At the time of writing this, there's no Service object for the correct
 * scope for Firebase Messaging, so here's an example of how this can be
 * done with provididing the scope manually.
 *
 * Info regarding authorization and requests can be found here:
 * https://firebase.google.com/docs/cloud-messaging/server
 */
require 'vendor/autoload.php';
$client = new Google_Client();
// Authentication with the GOOGLE_APPLICATION_CREDENTIALS environment variable
$client->useApplicationDefaultCredentials();
// Alternatively, provide the JSON authentication file directly.
$client->setAuthConfig(__DIR__.'/serviceaccount.json');
// Add the scope as a string (multiple scopes can be provided as an array)
$client->addScope('https://www.googleapis.com/auth/firebase.messaging');
// Returns an instance of GuzzleHttp\Client that authenticates with the Google API.
$httpClient = $client->authorize();
// Your Firebase project ID
$project = "my-hostel-app";
// Creates a notification for subscribers to the debug topic
$message = [
    "message" => [
        "token" => "dI2G2ewt02o:APA91bFPZHX2Tik3d9qTPYi5QSPbx1cKuinrhfiEf3h5QUkBnURZbKuRoZjy-xn6q1hAieOa3247BfGgolw02u9fK7qS-fDr5gPoefESP0nIWnFd0H0zz_pk7M5Cm4MUSmEFEcqfhNPx",
        "notification" => [
            "body" => "Alright modafaka",
            "title" => "Whats up moda faka",
        ]
    ]
];
// $message = [
//     "message" => [
//         "topic" => "hosteler-general",
//         "notification" => [
//             "body" => "Alright modafaka",
//             "title" => "Whats up moda faka",
//         ]
//     ]
// ];
// Send the Push Notification - use $response to inspect success or errors
for ($i=0; $i <5 ; $i++) {

  $message = [
      "message" => [
          "token" => "eQ2IJu3a3ME:APA91bH0Vj6NYewffJODZRJyvZPTX2inVknw9ebTV_wR_cTn1TqhfJHPoTLesu0DsRTrelkkBWl-1B8PJzc51WoS7zrtJQ7at_bW7-H8xFscyb7qSnFZxQyVE2yaNNm2B9b6re1Jw1jW",
          "notification" => [
              "body" => "Alright modafaka ".$i,
              "title" => "Whats up moda faka ",
          ]
      ]
  ];
  // code...
  $response = $httpClient->post("https://fcm.googleapis.com/v1/projects/my-hostel-app/messages:send", ['json' => $message]);
}
echo json_encode($response);

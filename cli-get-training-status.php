<?php
/**
 * Get training status (trained model)
 * Written by Ryan Yonzon <hello@ryanyonzon.com>
 */

set_include_path("vendor/google/apiclient/src/" . PATH_SEPARATOR . get_include_path());

require_once 'Google/Client.php';
require_once 'Google/Service/Prediction.php';

// Configuration stuff
$config = require_once 'config/Credentials.php';

// Create Google Client
$client = new Google_Client();
$client->setApplicationName("Prediction CLI");

$key = file_get_contents('key/' . $config->key_filename);

$client->setAssertionCredentials(new Google_Auth_AssertionCredentials(
    $config->service_account_name,
        array(
            'https://www.googleapis.com/auth/prediction'
            ),
        $key
    )
);
$client->setClientId($config->client_id);

// Create Prediction Service
$service = new Google_Service_Prediction($client);

$project_id = 'PROJECT-ID-HERE';
$trained_model_id = 'TRAINING-MODEL-ID-HERE';

$options = array(); // additional options
$status = $service->trainedmodels->get($project_id, $trained_model_id, $options);

// print the object result/status
print_r($status);

// Or print only the training status
// echo $status->getTrainingStatus() . "\n";

<?php
/**
 * Predict using Google hosted (sample) models
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

// Use Sentiment Predictor (Google hosted model) for demo purposes
$project_id = '414649711441';
$hosted_model_id = 'sample.sentiment';
$text_input = 'How about meeting up later?';

// Or Tag Categorizer (comment out the two lines above and uncomment the two below)
// $project_id = '414649711441';
// $hosted_model_name = 'sample.tagger';
// $text_input = 'What URL?';

$input_input = new Google_Service_Prediction_InputInput();

$input_input->setCsvInstance(array(
        $text_input
    ));

$input = new Google_Service_Prediction_Input();
$input->setInput($input_input);

$options = array(); // additional options
$result = $service->hostedmodels->predict($project_id, $hosted_model_id, $input, $options);

// print the object result
print_r($result);

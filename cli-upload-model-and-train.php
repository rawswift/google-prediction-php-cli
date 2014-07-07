<?php
/**
 * Upload and train model
 * Written by Ryan Yonzon <hello@ryanyonzon.com>
 */

set_include_path("vendor/google/apiclient/src/" . PATH_SEPARATOR . get_include_path());

require_once 'Google/Client.php';
require_once 'Google/Service/Storage.php';
require_once 'Google/Service/Prediction.php';

// Configuration stuff
$config = require_once 'config/Credentials.php';

$project_id = 'PROJECT-ID-HERE';
$training_model_id = 'TRAINING-MODEL-ID-HERE';

$bucket_name = 'BUCKET-NAME-HERE';
$local_file = "model/language_id.txt"; // model file to upload
$store_file_name = "language_id.txt"; // the filename that'll be used when stored on Google Storage

// Create Google Client
$client = new Google_Client();
$client->setApplicationName("Storage and Prediction CLI");

$key = file_get_contents('key/' . $config->key_filename);

$client->setAssertionCredentials(new Google_Auth_AssertionCredentials(
    $config->service_account_name,
        array(
            'https://www.googleapis.com/auth/prediction',
            'https://www.googleapis.com/auth/devstorage.read_write'
            ),
        $key
    )
);
$client->setClientId($config->client_id);

// Create Storage Service
$storage = new Google_Service_Storage($client);

// https://developers.google.com/storage/docs/json_api/v1/buckets/insert
$storage_object = new Google_Service_Storage_StorageObject();
$storage_object->setBucket($bucket_name);
$storage_object->setName($store_file_name);

$store_options = array(
        'data' => file_get_contents($local_file),
        'mimeType' => 'text/plain',
        'uploadType' => 'media' // media, multipart or resumable (https://developers.google.com/storage/docs/json_api/v1/how-tos/upload)
    );
echo "Uploading model... ";
$store_response = $storage->objects->insert($bucket_name, $storage_object, $store_options);
echo "Done\n";

echo "Training model... ";

// Create Prediction Service
$prediction = new Google_Service_Prediction($client);

$insert = new Google_Service_Prediction_Insert();
$insert->setId($training_model_id);
$insert->setStorageDataLocation($bucket_name . '/' . $store_file_name);

$training_options = array();
$training_result = $prediction->trainedmodels->insert($project_id, $insert, $training_options);

echo "Done\n";
echo "Training Result:\n";

// print the object result
print_r($training_result);

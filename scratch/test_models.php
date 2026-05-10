<?php
require 'vendor/autoload.php';
$apiKey = 'AIzaSyBgVQZg33XLYjnqD_Syt1DtdVWCGaOXxeI';
$client = \Gemini::client($apiKey);
try {
    $models = $client->models()->list();
    foreach ($models->models as $model) {
        echo $model->name . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

<?php
require __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;

// .envファイルを読み込む
$dotenv = Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

$client = new Google\Client();
$client->setAuthConfig("../google-api-key.json");
$client->setScopes(Google_Service_Sheets::SPREADSHEETS);

$service = new Google_Service_Sheets($client);
$spreadsheet_id = $_ENV['SPREADSHEET_ID'];
$response = $service->spreadsheets->get($spreadsheet_id);
echo $response->properties->title; // タイトル



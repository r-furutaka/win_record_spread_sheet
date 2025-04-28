<?php
require __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;

// .envファイルを読み込む
$dotenv = Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

// 環境変数を表示してみる
echo "APP_NAME (_ENV): " . $_ENV['APP_NAME'] . "\n";      // MyApp

phpinfo();

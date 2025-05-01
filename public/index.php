<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../app/Controller/IndexController.php';

$indexController = new IndexController();

// add_decktypeアクションがPOSTされた場合
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_decktype') {
    $decktype = htmlspecialchars($_POST['decktype'], ENT_QUOTES, 'UTF-8');
    $indexController->addDeckType($decktype);
}

$indexController->index();

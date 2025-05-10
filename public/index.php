<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../app/Controller/IndexController.php';
require __DIR__.'/../app/Services/GoogleSpreadSheetService.php';
require __DIR__.'/../app/UseCases/DateUseCase.php';

$indexController = new IndexController();

// add_decktypeアクションがPOSTされた場合
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_decktype') {
    $decktype = htmlspecialchars($_POST['decktype'], ENT_QUOTES, 'UTF-8');
    $indexController->addDeckType($decktype);
}
// add_recordアクションがPOSTされた場合
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_record') {
    $myDecktype = htmlspecialchars($_POST['my_decktype'], ENT_QUOTES, 'UTF-8');
    $opponentDecktype = htmlspecialchars($_POST['opponent_decktype'], ENT_QUOTES, 'UTF-8');
    $cube = htmlspecialchars($_POST['cube'], ENT_QUOTES, 'UTF-8');
    $indexController->addBattleRecord($myDecktype, $opponentDecktype, $cube);
}

$indexController->index();

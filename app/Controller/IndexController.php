<?php
use Dotenv\Dotenv;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Client;

class IndexController
{
    private $service;
    private $sheets;
    private $viewData;
    private $dateUseCase;

    public function __construct()
    {
        // .envファイルを読み込む
        $dotenv = Dotenv::createImmutable('../');
        $dotenv->load();

        $spreadsheetId = $_ENV['SPREADSHEET_ID'];
        $this->service = new GoogleSpreadSheetService($spreadsheetId);

        // スプレッドシートの情報を取得
        $this->sheets = $this->service->getSheets();

        // メッセージの初期化
        $this->viewData['message'] = '';

        // 日付のユースケースを初期化
        $this->dateUseCase = new DateUseCase();
    }
    /**
     * スプレッドシートの情報を取得し、デッキタイプの一覧を表示するメソッド
     */
    public function index()
    {
        // 新しいシートの名前
        $newSheetTitle = 'Decktype';
        $result = $this->service->addSheet($newSheetTitle, 1);
        if ($result) {
            $message = "新しいシートが追加されました: $newSheetTitle";
            $this->addMessage($message);
        }
        // 対戦記録の取得
        $battleRecords = $this->getBattleRecords();

        // デッキタイプの一覧を取得
        $decktypes = $this->getDeckTypes();

        // テンプレートに渡すデータ
        $this->viewData['sheets'] = $this->sheets;
        $this->viewData['decktypes'] = $decktypes;
        $this->viewData['battleRecords'] = $battleRecords;

        // テンプレートを読み込む
        $data = $this->viewData;
        include '../template/index.php';
    }
    /**
     * 対戦記録を取得するメソッド
     */
    private function getBattleRecords(): array
    {
        $month = $this->dateUseCase->getBattleMonth();
        $range = $month . '!A:D';
        $response = $this->service->getValues($range);
        // 新しい順に並び替え
        $response = array_reverse($response);
        return $response;
    }
    /**
     * デッキタイプを追加するメソッド
     *
     * @param string $decktype デッキタイプ名
     */
    public function addDeckType($decktype)
    {
        // デッキタイプを追加する処理
        $range = 'Decktype!A1:A';
        $values = [[$decktype]];
        $result = $this->service->addValues($range, $values);
        if($result) {
            $this->addMessage("デッキタイプ「{$decktype}」が追加されました。");
        } else {
            $this->addMessage("デッキタイプ「{$decktype}」の追加に失敗しました。");
        }
    }
    /**
     * 対戦記録を追加するメソッド
     *
     * @param string $myDecktype 自分のデッキタイプ
     * @param string $opponentDecktype 相手のデッキタイプ
     * @param string $cube キューブ
     */
    public function addBattleRecord($myDecktype, $opponentDecktype, $cube)
    {
        // 年月で対戦記録を追加する処理
        $month = $this->dateUseCase->getBattleMonth();
        $datetime = $this->dateUseCase->getBattleDatetime();

        // 対戦記録のシートを追加
        $newSheetTitle = $month;
        $result = $this->service->addSheet($newSheetTitle, 4);

        // 対戦記録を追加する処理
        $range = $newSheetTitle . '!A1:D';
        $values = [[$myDecktype, $opponentDecktype, $cube, $datetime]];
        $result = $this->service->addValues($range, $values);
        if($result) {
            $this->addMessage("対戦記録が追加されました。");
        } else {
            $this->addMessage("対戦記録の追加に失敗しました。");
        }
    }
    /**
     * デッキタイプの一覧を取得するメソッド
     *
     * @return array デッキタイプの一覧
     */
    private function getDeckTypes(): array
    {
        // デッキタイプの一覧を取得
        $range = 'Decktype!A:A';
        $decktypes = $this->service->getValues($range);
        return $decktypes;
    }
    /**
     * テンプレートに渡すデータを追加するメソッド
     *
     * @param string $key キー
     * @param string|array $value 値
     */
    private function addMessage(string $value): void
    {
        $key = 'message';
        if(isset($this->viewData[$key]) && $this->viewData[$key] !== '') {
            $this->viewData[$key] .= ', ' . $value;
        }
        else {
            $this->viewData[$key] = $value;
        }
    }
}
<?php
use Dotenv\Dotenv;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Client;

class IndexController
{
    private $service;
    private $sheets;
    private $viewData;

    public function __construct()
    {
        // .envファイルを読み込む
        $dotenv = Dotenv::createImmutable('../');
        $dotenv->load();

        $spreadsheetId = $_ENV['SPREADSHEET_ID'];
        $this->service = new GoogleSpreadSheetService($spreadsheetId);

        // スプレッドシートの情報を取得
        $this->sheets = $this->service->getSheets();
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
            $this->addViewData('message', $message);
        }
        // デッキタイプの一覧を取得
        $decktypes = $this->getDeckTypes();

        // テンプレートに渡すデータ
        $this->addViewData('sheets', $this->sheets);
        $this->addViewData('decktypes', $decktypes);

        // テンプレートを読み込む
        $data = $this->viewData;
        include '../template/index.php';
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
            $this->addViewData('message', "デッキタイプ「{$decktype}」が追加されました。");
        } else {
            $this->addViewData('message', "デッキタイプ「{$decktype}」の追加に失敗しました。");
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
        // 年月日時間を取得
        $date = new DateTime('now', new DateTimeZone('Asia/Tokyo')); // 日本時間に設定
        $month = $date->format('Y-m');
        $datetime = $date->format('Y-m-d H:i');

        // 対戦記録のシートを追加
        $newSheetTitle = $month;
        $result = $this->service->addSheet($newSheetTitle, 4);

        // 対戦記録を追加する処理
        $range = $newSheetTitle . '!A1:D';
        $values = [[$myDecktype, $opponentDecktype, $cube, $datetime]];
        $result = $this->service->addValues($range, $values);
        if($result) {
            $this->addViewData('message', "対戦記録が追加されました。");
        } else {
            $this->addViewData('message', "対戦記録の追加に失敗しました。");
        }
    }
    /**
     * デッキタイプの一覧を取得するメソッド
     *
     * @return array デッキタイプの一覧
     */
    function getDeckTypes(): array
    {
        // デッキタイプの一覧を取得
        $decktypeRange = 'Decktype!A:A';
        $decktypes = $this->service->getValues($decktypeRange);
        return $decktypes;
    }
    /**
     * テンプレートに渡すデータを追加するメソッド
     *
     * @param string $key キー
     * @param string|array $value 値
     */
    function addViewData(string $key, string|array $value): void
    {
        if(isset($this->viewData[$key])) {
            if(is_string($this->viewData[$key])) {
                $this->viewData[$key] .= ', ' . $value;
            } else if(is_array($this->viewData[$key])) {
                $this->viewData[$key][] = $value;
            }
        }
        else {
            $this->viewData[$key] = $value;
        }
    }
}
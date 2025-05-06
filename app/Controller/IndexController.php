<?php
use Dotenv\Dotenv;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Client;

class IndexController
{
    private $service;
    private $spreadsheetId;
    private $sheets;
    private $viewData;

    public function __construct()
    {
        // .envファイルを読み込む
        $dotenv = Dotenv::createImmutable('../');
        $dotenv->load();

        $this->spreadsheetId = $_ENV['SPREADSHEET_ID'];

        $client = new Client();
        $client->setAuthConfig("../google-api-key.json");
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);

        $this->service = new Google_Service_Sheets($client);

        // スプレッドシートの情報を取得
        $response = $this->service->spreadsheets->get($this->spreadsheetId);
        $this->sheets = $response->getSheets();
    }
    /**
     * スプレッドシートの情報を取得し、デッキタイプの一覧を表示するメソッド
     */
    public function index()
    {
        $message = '';

        // 新しいシートの名前
        $newSheetTitle = 'Decktype';
        $result = $this->addSheet($newSheetTitle, 1);
        if ($result) {
            $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
            $message = "新しいシートが追加されました: $newSheetTitle";
        }
        // デッキタイプの一覧を取得
        $decktypes = $this->getDeckTypes();

        // テンプレートに渡すデータ
        $this->addViewData('sheets', $this->sheets);
        $this->addViewData('decktypes', $decktypes);
        $this->addViewData('message', $message);

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
        $body = new Google_Service_Sheets_ValueRange(['values' => $values]);

        $params = ['valueInputOption' => 'RAW'];
        try {
            $result = $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);
            echo "デッキタイプ '$decktype' が追加されました。";
        } catch (Exception $e) {
            echo "エラーが発生しました: " . $e->getMessage();
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
        $result = $this->addSheet($newSheetTitle, 4);

        // 対戦記録を追加する処理
        $range = $newSheetTitle . '!A1:D';
        $values = [[$myDecktype, $opponentDecktype, $cube, $datetime]];
        $body = new Google_Service_Sheets_ValueRange(['values' => $values]);

        $params = ['valueInputOption' => 'RAW'];
        try {
            $result = $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);
            echo "対戦記録が追加されました。";
        } catch (Exception $e) {
            echo "エラーが発生しました: " . $e->getMessage();
        }
    }
    /**
     * 新しいシートを追加するメソッド
     *
     * @param string $sheetName シート名
     * @param int $columnCount 列数
     * @return bool 成功した場合はtrue、失敗した場合はfalse
     */
    public function addSheet(string $sheetName, int $columnCount)
    {
        // シートの有無を確認
        $sheetExists = false;
        foreach ($this->sheets as $sheet) {
            if ($sheet->properties->title === $sheetName) {
                $sheetExists = true;
                break;
            }
        }
        if ($sheetExists) {
            return false; // シートが既に存在する場合は何もしない
        }

        // 新しいシートを追加する処理
        $requests = [
            new Google_Service_Sheets_Request([
                'addSheet' => [
                    'properties' => [
                        'title' => $sheetName,
                        'gridProperties' => [
                            'rowCount' => 1000,
                            'columnCount' => $columnCount
                        ]
                    ]
                ]
            ])
        ];

        $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $requests
        ]);
        $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
        return true;
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
        $decktypeResponse = $this->service->spreadsheets_values->get($this->spreadsheetId, $decktypeRange);
        $decktypes = [];
        if (null !== $decktypeResponse->getValues()) {
            foreach ($decktypeResponse->getValues() as $row) {
                if (isset($row[0])) {
                    $decktypes[] = $row[0];
                }
            }
        }
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
        $this->viewData[$key] = $value;
    }
}
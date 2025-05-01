<?php
use Dotenv\Dotenv;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Client;

class IndexController
{
    private $service;
    private $spreadsheetId;

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
    }
    public function index()
    {
        // スプレッドシートの情報を取得
        $response = $this->service->spreadsheets->get($this->spreadsheetId);
        $sheets = $response->getSheets();

        // 新しいシートの名前
        $newSheetTitle = 'Decktype';

        // 既存のシート名を確認
        $sheetExists = false;
        foreach ($sheets as $sheet) {
            if ($sheet->properties->title === $newSheetTitle) {
                $sheetExists = true;
                break;
            }
        }

        if ($sheetExists) {
            $message = "シート '$newSheetTitle' は既に存在します。";
        } else {
            // 新しいシートを追加するリクエストを作成
            $requests = [
                new Google_Service_Sheets_Request([
                    'addSheet' => [
                        'properties' => [
                            'title' => $newSheetTitle,
                            'gridProperties' => [
                                'rowCount' => 1000,
                                'columnCount' => 1
                            ]
                        ]
                    ]
                ])
            ];

            $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => $requests
            ]);

            $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
            $message = "新しいシートが追加されました: $newSheetTitle";
        }

        // テンプレートを読み込む
        include '../template/index.php';
    }
    public function addDeckType($decktype)
    {
        // デッキタイプを追加する処理
        $range = 'Decktype!A1:A';
        $values = [[$decktype]];
        $body = new Google_Service_Sheets_ValueRange(['values' => $values]);

        $params = ['valueInputOption' => 'RAW'];
        $result = $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);

        // メッセージを表示
        echo "デッキタイプ '$decktype' が追加されました。";
    }
}
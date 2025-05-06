<?php
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Client;

class GoogleSpreadSheetService
{
    private $service;
    private $sheets;
    private $spreadsheetId;

    public function __construct($spreadsheetId)
    {
        $this->spreadsheetId = $spreadsheetId;

        $client = new Client();
        $client->setAuthConfig("/var/www/html/google-api-key.json");
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);

        $this->service = new Google_Service_Sheets($client);

        // スプレッドシートの情報を取得
        $response = $this->service->spreadsheets->get($this->spreadsheetId);
        $this->sheets = $response->getSheets();
    }
    public function getSheets()
    {
        return $this->sheets;
    }
    public function getValues($range)
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $types = [];
        if (null !== $response->getValues()) {
            foreach ($response->getValues() as $row) {
                if (isset($row[0])) {
                    $types[] = $row[0];
                }
            }
        }
        return $types;
    }
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
    public function addValues($range, $values): bool
    {
        $body = new Google_Service_Sheets_ValueRange(['values' => $values]);

        $params = ['valueInputOption' => 'RAW'];
        try {
            $result = $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);
        } catch (Exception $e) {
            echo "エラーが発生しました: " . $e->getMessage();
            return false;
        }
        return true;
    }
}
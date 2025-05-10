<?php
class DateUseCase
{
    private $date;

    public function __construct()
    {
        // 日本時間に設定
        $this->date = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    }
    /**
     * 現在使用している対戦記録のシートの日時を取得するメソッド
     *
     * @return string 現在の年月
     */
    function getBattleMonth()
    {
        // 年月を取得
        $month = $this->date->format('Y-m');
        return $month;
    }
    /**
     * 現在の日時を取得するメソッド
     *
     * @return string 現在の日時
     */
    function getBattleDatetime()
    {
        // 年月日時間を取得
        $datetime = $this->date->format('Y-m-d H:i');
        return $datetime;
    }
}
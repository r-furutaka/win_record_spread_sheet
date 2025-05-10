# 出来ること
- Googleスプレッドシートに、マーベルスナップの勝敗記録を残す
  - 自分のデッキ
  - 相手のデッキ
  - キューブの増減
- デッキタイプはselectで取得可能
- 無いデッキタイプは追記可能
- 今月の対戦記録が同じ画面に表示される

# アプリを動かすための必須要素
- .env
  - スプレッドシートのID
  - DateTimeZone
- google-api-key.json
  - Google Sheets APIの認証情報のファイルをリネームしたもの
  - 取得方法はこちら参考
    - https://www.otsuka-bs.co.jp/web-creation/blog/archive/20230904-03.html  

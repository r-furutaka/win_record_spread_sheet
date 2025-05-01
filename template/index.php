<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マベスナ勝敗記録</title>
</head>
<body>
    <h2>シート一覧</h2>
    <ul>
        <?php foreach ($sheets as $sheet): ?>
            <li>Sheet ID: <?= htmlspecialchars($sheet->properties->sheetId, ENT_QUOTES, 'UTF-8') ?>, 
                Title: <?= htmlspecialchars($sheet->properties->title, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
    <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <hr>
    <h2>デッキタイプの追加</h2>
    <form action="" method="post">
        <input type="hidden" name="action" value="add_decktype">
        <label for="decktype">デッキタイプ名:</label>
        <input type="text" id="decktype" name="decktype" required>
        <button type="submit">追加</button>
    </form>
</body>
</html>
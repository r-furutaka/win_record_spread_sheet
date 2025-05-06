<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マベスナ勝敗記録</title>
</head>
<body>
    <p><?= htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8') ?></p>
    <h2>対戦記録</h2>
    <form action="" method="post">
        <input type="hidden" name="action" value="add_record">
        <label for="my_decktype">自分:</label>
        <select id="my_decktype" name="my_decktype" required>
            <?php foreach ($data['decktypes'] as $decktype): ?>
                <option value="<?= htmlspecialchars($decktype, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($decktype, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <label for="opponent_decktype">相手:</label>
        <select id="opponent_decktype" name="opponent_decktype" required>
            <?php foreach ($data['decktypes'] as $decktype): ?>
                <option value="<?= htmlspecialchars($decktype, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($decktype, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <label for="cube">キューブ:</label>
        <select id="cube" name="cube" required>
            <option value="+8">+8</option>
            <option value="+4">+4</option>
            <option value="+2">+2</option>
            <option value="+1">+1</option>
            <option value="0" selected>引き分け</option>
            <option value="-1">-1</option>
            <option value="-2">-2</option>
            <option value="-4">-4</option>
            <option value="-8">-8</option>
        </select>
        <button type="submit">記録</button>
    </form>
    <hr>
    <h2>デッキタイプの追加</h2>
    <form action="" method="post">
        <input type="hidden" name="action" value="add_decktype">
        <label for="decktype">デッキタイプ名:</label>
        <input type="text" id="decktype" name="decktype" required>
        <button type="submit">追加</button>
    </form>
    <hr>
    <h2>System</h2>
    <ul>
        <?php foreach ($data['sheets'] as $sheet): ?>
            <li>Sheet ID: <?= htmlspecialchars($sheet->properties->sheetId, ENT_QUOTES, 'UTF-8') ?>, 
                Title: <?= htmlspecialchars($sheet->properties->title, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
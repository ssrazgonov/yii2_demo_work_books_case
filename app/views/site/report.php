<?php

$this->title = 'Отчет: ТОП-10 авторов за ' . $year . ' год';
?>

<div class="site-report">
    <h1><?= $this->title; ?></h1>

    <div style="margin-bottom: 20px;">
        <form method="get" action="<?= \yii\helpers\Url::to(['site/report']); ?>">
            <label for="year">Выберите год:</label>
            <select name="year" id="year">
                <?php for ($y = date('Y'); $y >= 1800; $y--): ?>
                    <option value="<?= $y; ?>" <?= $y == $year ? 'selected' : ''; ?>>
                        <?= $y; ?>
                    </option>
                <?php endfor; ?>
            </select>
            <input type="hidden" name="r" value="site/report">
            <button type="submit">Показать</button>
        </form>
    </div>

    <?php if (empty($topAuthors)): ?>
        <p>Нет данных за выбранный год.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Место</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Автор</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Количество книг</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topAuthors as $index => $author): ?>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $index + 1; ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($author['name']); ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $author['book_count']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
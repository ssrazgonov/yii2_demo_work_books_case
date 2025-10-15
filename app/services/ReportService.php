<?php

namespace app\services;

use Yii;

class ReportService
{
    public function getTopAuthorsByYear(int $year): array
    {
        return Yii::$app->db->createCommand("
            SELECT
                a.name,
                COUNT(b.id) as book_count
            FROM author a
            JOIN book_author ba ON a.id = ba.author_id
            JOIN book b ON ba.book_id = b.id
            WHERE b.year = :year
            GROUP BY a.id, a.name
            ORDER BY book_count DESC
            LIMIT 10
        ")->bindValue(':year', $year)->queryAll();
    }
}
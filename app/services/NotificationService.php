<?php

namespace app\services;

use app\models\Subscription;
use app\models\Author;
use Yii;

class NotificationService
{
    public function notifySubscribers(int $authorId, string $bookTitle): void
    {
        $author = Author::findOne($authorId);
        if (!$author) {
            return;
        }

        $subscriptions = Subscription::find()->where(['author_id' => $authorId])->all();

        foreach ($subscriptions as $subscription) {
            $this->sendSms($subscription->phone, "Новая книга '{$bookTitle}' от автора {$author->name}");
        }
    }

    private function sendSms(string $phone, string $message): void
    {
        Yii::info("SMS: Телефон: {$phone}, Сообшение: {$message}", 'notification');
    }
}
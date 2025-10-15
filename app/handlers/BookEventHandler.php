<?php

namespace app\handlers;

use app\events\BookCreatedEvent;
use app\jobs\SendNotificationJob;
use Yii;

class BookEventHandler
{
    public static function handleBookCreated(BookCreatedEvent $event): void
    {
        foreach ($event->authorIds as $authorId) {
            Yii::$app->queue->push(new SendNotificationJob([
                'authorId' => $authorId,
                'bookTitle' => $event->book->title,
            ]));
        }
    }
}
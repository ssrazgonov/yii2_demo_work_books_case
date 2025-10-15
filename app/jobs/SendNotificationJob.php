<?php

namespace app\jobs;

use app\services\NotificationService;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use Yii;

class SendNotificationJob extends BaseObject implements JobInterface
{
    public int $authorId;
    public string $bookTitle;

    public function execute($queue): void
    {
        $notificationService = new NotificationService();
        $notificationService->notifySubscribers($this->authorId, $this->bookTitle);
    }
}
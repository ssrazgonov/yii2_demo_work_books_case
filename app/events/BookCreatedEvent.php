<?php

namespace app\events;

use app\models\Book;
use yii\base\Event;

class BookCreatedEvent extends Event
{
    public Book $book;
    public array $authorIds;

    public function __construct(Book $book, array $authorIds, $config = [])
    {
        $this->book = $book;
        $this->authorIds = $authorIds;
        parent::__construct($config);
    }
}
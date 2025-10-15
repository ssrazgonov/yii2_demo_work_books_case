<?php

namespace app\services;

use app\dto\AuthorDto;
use app\models\Author;
use app\models\BookAuthor;
use Yii;
use yii\data\ActiveDataProvider;

class AuthorService
{
    public function getAllAuthors(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Author::find(),
        ]);
    }

    public function getAuthorById(int $id): ?Author
    {
        return Author::findOne($id);
    }

    public function createAuthor(AuthorDto $dto): Author
    {
        $author = new Author();
        $author->name = trim($dto->name);

        if (!$author->validate()) {
            throw new \InvalidArgumentException('Некорректные данные автора: ' . implode(', ', $author->getErrorSummary(true)));
        }

        if (!$author->save()) {
            throw new \RuntimeException('Не удалось сохранить автора');
        }

        return $author;
    }

    public function updateAuthor(int $id, AuthorDto $dto): Author
    {
        $author = $this->getAuthorById($id) ?? throw new \RuntimeException("Автор с ID {$id} не найден");

        $author->name = trim($dto->name);

        if (!$author->validate()) {
            throw new \InvalidArgumentException('Некорректные данные автора: ' . implode(', ', $author->getErrorSummary(true)));
        }

        if (!$author->save()) {
            throw new \RuntimeException('Не удалось обновить автора');
        }

        return $author;
    }

    public function deleteAuthor(int $id): bool
    {
        $author = $this->getAuthorById($id);
        if (!$author) {
            return false;
        }

        return (bool)$author->delete();
    }
}
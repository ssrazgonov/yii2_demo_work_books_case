<?php

namespace app\services;

use app\dto\BookDto;
use app\models\Book;
use app\models\BookAuthor;
use app\models\Author;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class BookService
{
    public function getAllBooks(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Book::find()->with('authors'),
        ]);
    }

    public function getBookById(int $id): ?Book
    {
        return Book::findOne($id);
    }

    public function createBook(BookDto $dto): Book
    {
        $book = new Book();
        $book->title = trim($dto->title);
        $book->year = $dto->year;
        $book->description = $dto->description ? trim($dto->description) : null;
        $book->isbn = $dto->isbn ? trim($dto->isbn) : null;
        $book->cover_image = $dto->cover_image ? trim($dto->cover_image) : null;

        if (!$book->validate()) {
            throw new \InvalidArgumentException('Некорректные данные книги: ' . implode(', ', $book->getErrorSummary(true)));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$book->save()) {
                throw new \RuntimeException('Не удалось сохранить книгу');
            }
            $this->updateBookAuthors($book->id, $dto->authorIds);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $book;
    }

    public function updateBook(int $id, BookDto $dto): Book
    {
        $book = $this->getBookById($id) ?? throw new \RuntimeException("Книга с ID {$id} не найдена");

        $book->title = trim($dto->title);
        $book->year = $dto->year;
        $book->description = $dto->description ? trim($dto->description) : null;
        $book->isbn = $dto->isbn ? trim($dto->isbn) : null;
        $book->cover_image = $dto->cover_image ? trim($dto->cover_image) : null;

        if (!$book->validate()) {
            throw new \InvalidArgumentException('Некорректные данные книги: ' . implode(', ', $book->getErrorSummary(true)));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$book->save()) {
                throw new \RuntimeException('Не удалось обновить книгу');
            }
            $this->updateBookAuthors($book->id, $dto->authorIds);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $book;
    }

    public function deleteBook(int $id): bool
    {
        $book = $this->getBookById($id);
        if (!$book) {
            return false;
        }

        return (bool)$book->delete();
    }

    public function getAuthorsList(): array
    {
        return ArrayHelper::map(Author::find()->all(), 'id', 'name');
    }

    private function updateBookAuthors(int $bookId, array $authorIds): void
    {
        $book = Book::findOne($bookId);
        if (!$book) {
            return;
        }

        $book->unlinkAll('authors', true);

        array_map(function (int $authorId) use ($book) {
            $author = Author::findOne($authorId);
            if ($author) {
                $book->link('authors', $author);
            }
        }, $authorIds);
    }
}
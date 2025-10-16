<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class BookForm extends Model
{
    public string $title = '';
    public int $year = 0;
    public ?string $description = null;
    public ?string $isbn = null;
    public ?string $cover_image = null;
    public array $authorIds = [];

    public string $coverImageFile = '';

    private ?int $_bookId = null;

    public function rules(): array
    {
        return [
            [['title', 'year'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => date('Y') + 1],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 13],
            [['isbn'], 'unique', 'targetClass' => Book::class, 'targetAttribute' => 'isbn', 'filter' => function($query) {
                $bookId = $this->getBookId();
                if ($bookId !== null) {
                    $query->andWhere(['!=', 'id', $bookId]);
                }
                return $query;
            }],
            [['cover_image'], 'string', 'max' => 255],
            [['authorIds'], 'each', 'rule' => ['integer']],
            [['authorIds'], 'validateAuthors'],
            [['coverImageFile'], 'file', 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 5 * 1024 * 1024, 'skipOnEmpty' => true],
        ];
    }

    public function validateAuthors($attribute, $params): void
    {
        if (empty($this->$attribute)) {
            $this->addError($attribute, 'Необходимо выбрать хотя бы одного автора.');
            return;
        }

        $existingAuthors = Author::find()->where(['id' => $this->$attribute])->count();
        if ($existingAuthors !== count($this->$attribute)) {
            $this->addError($attribute, 'Один или несколько выбранных авторов не существуют.');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Название книги',
            'year' => 'Год издания',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_image' => 'Обложка',
            'authorIds' => 'Авторы',
            'coverImageFile' => 'Файл обложки',
        ];
    }

    public function loadBook(Book $book): void
    {
        $this->title = $book->title;
        $this->year = $book->year;
        $this->description = $book->description;
        $this->isbn = $book->isbn;
        $this->cover_image = $book->cover_image;
        $this->authorIds = array_column($book->bookAuthors, 'author_id');
    }

    public function getBookId(): ?int
    {
        return $this->_bookId;
    }

    public function setBookId(int $id): void
    {
        $this->_bookId = $id;
    }
}
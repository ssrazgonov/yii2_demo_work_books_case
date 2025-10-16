<?php

namespace app\models;

use Yii;
use yii\base\Model;

class AuthorForm extends Model
{
    public string $name = '';
    private ?int $_authorId = null;

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique', 'targetClass' => Author::class, 'targetAttribute' => 'name', 'filter' => function($query) {
                if ($this->getAuthorId() !== null) {
                    $query->andWhere(['!=', 'id', $this->getAuthorId()]);
                }
                return $query;
            }],
            [['name'], 'trim'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'ФИО автора',
        ];
    }

    public function loadAuthor(Author $author): void
    {
        $this->name = $author->name;
    }

    public function getAuthorId(): ?int
    {
        return $this->_authorId;
    }

    public function setAuthorId(int $id): void
    {
        $this->_authorId = $id;
    }

    public function isNewRecord(): bool
    {
        return $this->getAuthorId() === null;
    }
}
<?php

namespace app\models;

use Yii;

class Author extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'author';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя автора',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['author_id' => 'id']);
    }

    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])->viaTable('book_author', ['author_id' => 'id']);
    }

    public function getSubscriptions()
    {
        return $this->hasMany(Subscription::class, ['author_id' => 'id']);
    }

    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
        ];
    }
}
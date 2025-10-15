<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Subscription extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'subscription';
    }

    public function rules(): array
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id', 'created_at'], 'integer'],
            [['phone'], 'string', 'max' => 15],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'author_id' => 'ID автора',
            'phone' => 'Телефон',
            'created_at' => 'Дата подписки',
        ];
    }

    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }
}
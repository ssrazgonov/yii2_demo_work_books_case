<?php

namespace app\models;

use Yii;

class Subscription extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'subscription';
    }

    public function rules()
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id', 'created_at'], 'integer'],
            [['phone'], 'string', 'max' => 15],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
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

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }
}
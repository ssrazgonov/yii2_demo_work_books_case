<?php

/** @var yii\web\View $this */
/** @var \app\models\Author[] $authors */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Каталог книг';
?>

<div class="site-index">
    <h1>Каталог книг</h1>

    <?php foreach ($authors as $author): ?>
        <div style="margin-bottom: 30px; border: 1px solid #ccc; padding: 10px;">
            <h2><?php echo Html::encode($author->name); ?></h2>

            <h3>Книги:</h3>
            <ul>
                <?php foreach ($author->books as $book): ?>
                    <li>
                        <strong><?php echo Html::encode($book->title); ?></strong>
                        (<?php echo Html::encode($book->year); ?>)
                        <?php if ($book->isbn): ?>
                            - ISBN: <?php echo Html::encode($book->isbn); ?>
                        <?php endif; ?>
                        <?php if ($book->description): ?>
                            <br><em><?php echo Html::encode($book->description); ?></em>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h4>Подписаться на новые книги автора:</h4>
            <?php $form = ActiveForm::begin([
                'action' => ['site/subscribe'],
                'method' => 'post',
            ]); ?>

            <?php echo $form->field($model ?? new \app\models\Subscription(), 'phone')
                ->textInput(['placeholder' => '+7XXXXXXXXXX'])
                ->label('Номер телефона'); ?>

            <?php echo Html::hiddenInput('author_id', $author->id); ?>

            <div class="form-group">
                <?php echo Html::submitButton('Подписаться', ['class' => 'btn btn-primary']); ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    <?php endforeach; ?>
</div>

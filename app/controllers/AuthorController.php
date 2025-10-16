<?php

namespace app\controllers;

use app\dto\AuthorDto;
use app\models\Author;
use app\models\AuthorForm;
use app\services\AuthorService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AuthorController extends BaseController
{
    public function __construct(
        $id,
        $module,
        private AuthorService $authorService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): Response|string
    {
        $dataProvider = $this->authorService->getAllAuthors();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        $model = $this->authorService->getAuthorById($id);
        if (!$model) {
            throw new NotFoundHttpException('Автор не найден.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate(): Response|string
    {
        $model = new AuthorForm();

        return $this->handlePostRequest(
            function () use ($model) {
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    return $this->authorService->createAuthor(
                        new AuthorDto($model->name)
                    );
                }
                return null;
            },
            'Автор успешно создан.',
            'Ошибка при сохранении автора.',
            ['view', 'id' => '__MODEL_ID__']
        ) ?? $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $author = $this->findModel($id);
        $model = new AuthorForm();
        $model->loadAuthor($author);
        $model->setAuthorId($id);

        return $this->handlePostRequest(
            function () use ($model, $id) {
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    return $this->authorService->updateAuthor(
                        $id,
                        new AuthorDto($model->name)
                    );
                }
                return null;
            },
            'Автор успешно обновлен.',
            'Ошибка при обновлении автора.',
            ['view', 'id' => '__MODEL_ID__']
        ) ?? $this->render('update', ['model' => $model]);
    }

    public function actionDelete(int $id): Response
    {
        $this->findModel($id);

        try {
            $this->authorService->deleteAuthor($id);
            Yii::$app->session->setFlash('success', 'Автор успешно удален.');
        } catch (\RuntimeException $e) {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении автора.');
            Yii::error($e->getMessage(), __METHOD__);
        }

        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Author
    {
        return $this->authorService->getAuthorById($id)
            ?? throw new NotFoundHttpException('Автор не найден.');
    }

}
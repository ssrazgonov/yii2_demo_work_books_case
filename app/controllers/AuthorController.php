<?php

namespace app\controllers;

use app\dto\AuthorDto;
use app\models\Author;
use app\services\AuthorService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AuthorController extends Controller
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
        return $this->handlePostRequest(
            fn() => $this->authorService->createAuthor(
                new AuthorDto(Yii::$app->request->post('Author', [])['name'] ?? '')
            ),
            'Автор успешно создан.',
            'Ошибка при сохранении автора.',
            ['view', 'id' => '__MODEL_ID__']
        ) ?? $this->render('create', ['model' => new Author()]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        return $this->handlePostRequest(
            fn() => $this->authorService->updateAuthor(
                $id,
                new AuthorDto(Yii::$app->request->post('Author', [])['name'] ?? '')
            ),
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

    protected function handlePostRequest(callable $action, string $successMessage, string $errorMessage, array $redirectRoute): ?Response
    {
        if (!Yii::$app->request->isPost) {
            return null;
        }

        try {
            $model = $action();
            Yii::$app->session->setFlash('success', $successMessage);
            $redirectRoute = str_replace('__MODEL_ID__', (string)$model->id, $redirectRoute);
            return $this->redirect($redirectRoute);
        } catch (\InvalidArgumentException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (\RuntimeException $e) {
            Yii::$app->session->setFlash('error', $errorMessage);
            Yii::error($e->getMessage(), __METHOD__);
        }

        return null;
    }
}
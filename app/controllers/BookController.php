<?php

namespace app\controllers;

use app\dto\BookDto;
use app\models\Book;
use app\services\BookService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BookController extends Controller
{
    public function __construct(
        $id,
        $module,
        private BookService $bookService,
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
                        'allow' => true,
                        'roles' => ['@'],
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
        $dataProvider = $this->bookService->getAllBooks();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        $model = $this->bookService->getBookById($id);
        if (!$model) {
            throw new NotFoundHttpException('Книга не найдена.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate(): Response|string
    {
        return $this->handlePostRequest(
            function () {
                $postData = Yii::$app->request->post('Book', []);
                return $this->bookService->createBook(new BookDto(
                    title: $postData['title'] ?? '',
                    year: (int)($postData['year'] ?? 0),
                    description: $postData['description'] ?? null,
                    isbn: $postData['isbn'] ?? null,
                    cover_image: $postData['cover_image'] ?? null,
                    authorIds: $postData['authorIds'] ?? []
                ));
            },
            'Книга успешно создана.',
            'Ошибка при сохранении книги.',
            ['view', 'id' => '__MODEL_ID__']
        ) ?? $this->render('create', [
            'model' => new Book(),
            'authors' => $this->bookService->getAuthorsList(),
            'selectedAuthors' => [],
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        return $this->handlePostRequest(
            function () use ($id) {
                $postData = Yii::$app->request->post('Book', []);
                return $this->bookService->updateBook($id, new BookDto(
                    title: $postData['title'] ?? '',
                    year: (int)($postData['year'] ?? 0),
                    description: $postData['description'] ?? null,
                    isbn: $postData['isbn'] ?? null,
                    cover_image: $postData['cover_image'] ?? null,
                    authorIds: $postData['authorIds'] ?? []
                ));
            },
            'Книга успешно обновлена.',
            'Ошибка при обновлении книги.',
            ['view', 'id' => '__MODEL_ID__']
        ) ?? $this->render('update', [
            'model' => $model,
            'authors' => $this->bookService->getAuthorsList(),
            'selectedAuthors' => ArrayHelper::getColumn($model->bookAuthors, 'author_id'),
        ]);
    }

    public function actionDelete(int $id): Response
    {
        $this->findModel($id);

        try {
            $this->bookService->deleteBook($id);
            Yii::$app->session->setFlash('success', 'Книга успешно удалена.');
        } catch (\RuntimeException $e) {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении книги.');
            Yii::error($e->getMessage(), __METHOD__);
        }

        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Book
    {
        return $this->bookService->getBookById($id)
            ?? throw new NotFoundHttpException('Книга не найдена.');
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
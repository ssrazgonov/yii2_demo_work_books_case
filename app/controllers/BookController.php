<?php

namespace app\controllers;

use app\dto\BookDto;
use app\models\Book;
use app\models\BookForm;
use app\services\BookService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class BookController extends BaseController
{
    private BookService $bookService;

    public function __construct(
        $id,
        $module,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        $this->bookService = Yii::$app->get(BookService::SERVICE_NAME);
        parent::init();
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
        $model = new BookForm();

        return $this->handlePostRequest(
            function () use ($model) {
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $coverImage = UploadedFile::getInstance($model, 'coverImageFile');

                    return $this->bookService->createBook(new BookDto(
                        title: $model->title,
                        year: $model->year,
                        description: $model->description,
                        isbn: $model->isbn,
                        cover_image: $model->cover_image,
                        authorIds: is_array($model->authorIds) ? $model->authorIds : [],
                        coverImageFile: $coverImage
                    ));
                }
                return null;
            },
            'Книга успешно создана.',
            'Ошибка при сохранении книги.',
            ['view', 'id' => '__MODEL_ID__']
        ) ?? $this->render('create', [
            'model' => $model,
            'authors' => $this->bookService->getAuthorsList(),
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $book = $this->findModel($id);
        $model = new BookForm();
        $model->loadBook($book);
        $model->setBookId($id);

        return $this->handlePostRequest(
            function () use ($model, $id) {
                $model->setBookId($id); // Ensure bookId is set before validation
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $coverImage = UploadedFile::getInstance($model, 'coverImageFile');

                    return $this->bookService->updateBook($id, new BookDto(
                        title: $model->title,
                        year: $model->year,
                        description: $model->description,
                        isbn: $model->isbn,
                        cover_image: $model->cover_image,
                        authorIds: is_array($model->authorIds) ? $model->authorIds : [],
                        coverImageFile: $coverImage
                    ));
                }
                return null;
            },
            'Книга успешно обновлена.',
            'Ошибка при обновлении книги.',
            ['view', 'id' => '__MODEL_ID__']
        ) ?? $this->render('update', [
            'model' => $model,
            'authors' => $this->bookService->getAuthorsList(),
            'id' => $id,
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


}
<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Subscription;
use app\services\ReportService;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionIndex()
    {
        $authors = \app\models\Author::find()->with('books')->all();

        return $this->render('index', [
            'authors' => $authors,
        ]);
    }

    public function actionReport()
    {
        $year = Yii::$app->request->get('year', date('Y'));
        $reportService = new ReportService();

        $topAuthors = $reportService->getTopAuthorsByYear($year);

        return $this->render('report', [
            'topAuthors' => $topAuthors,
            'year' => $year,
        ]);
    }

    public function actionSubscribe()
    {
        $authorId = Yii::$app->request->post('author_id');
        $phone = Yii::$app->request->post('Subscription')['phone'] ?? null;

        if (!$authorId || !$phone) {
            Yii::$app->session->setFlash('error', 'Не указан автор или телефон.');
            return $this->redirect(['index']);
        }

        $existingSubscription = Subscription::find()
            ->where(['author_id' => $authorId, 'phone' => $phone])
            ->exists();

        if ($existingSubscription) {
            Yii::$app->session->setFlash('error', 'Вы уже подписаны на уведомления этого автора.');
            return $this->redirect(['index']);
        }

        $subscription = new Subscription();
        $subscription->author_id = $authorId;
        $subscription->phone = $phone;
        $subscription->created_at = time();

        if ($subscription->save()) {
            Yii::$app->session->setFlash('success', 'Вы успешно подписались на уведомления об новых книгах автора.');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при подписке.');
        }

        return $this->redirect(['index']);
    }
}

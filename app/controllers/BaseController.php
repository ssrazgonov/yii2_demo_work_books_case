<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

abstract class BaseController extends Controller
{
    protected function handlePostRequest(callable $action, string $successMessage, string $errorMessage, array $redirectRoute): ?Response
    {
        if (!Yii::$app->request->isPost) {
            return null;
        }

        try {
            $model = $action();
            if ($model === null) {
                return null;
            }
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
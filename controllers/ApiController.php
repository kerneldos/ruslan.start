<?php

namespace app\controllers;

use app\components\jobs\SambaIndexingJob;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class ApiController extends Controller {
    /**
     * @throws NotFoundHttpException
     */
    public function actionSambaIndexing() {
        $this->enableCsrfValidation = false;

        $file = Yii::$app->request->post('file');

        if (!empty($file)) {
            Yii::$app->queue->push(new SambaIndexingJob(['file' => $file]));
        }

        throw new NotFoundHttpException('File not found');
    }
}

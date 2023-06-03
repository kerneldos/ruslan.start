<?php

namespace app\controllers;

use app\components\jobs\SambaIndexingJob;
use Yii;
use yii\web\Controller;


class ApiController extends Controller {
    public function actionSambaIndexing() {
        $file = Yii::$app->request->post('file');

        Yii::$app->queue->push(new SambaIndexingJob(['file' => $file]));
    }
}

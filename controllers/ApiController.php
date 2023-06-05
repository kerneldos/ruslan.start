<?php

namespace app\controllers;

use app\components\jobs\SambaIndexingJob;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class ApiController extends Controller {
    /**
     * @throws NotFoundHttpException
     */
    public function actionSambaIndexing() {
        Yii::$app->request->enableCsrfValidation = false;
        $this->enableCsrfValidation = false;

        $file = Yii::$app->request->post('file');

        if (!empty($file)) {
            Yii::$app->queue->push(new SambaIndexingJob(['file' => $file]));
        }

        throw new NotFoundHttpException('File not found');
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function actionSambaDownload(string $path): array {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['content' => file_get_contents('Yii::getAlias($path)')];
    }
}

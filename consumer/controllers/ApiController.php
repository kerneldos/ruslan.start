<?php

namespace consumer\controllers;

use consumer\components\jobs\SambaIndexingJob;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class ApiController extends Controller {
    /**
     * @throws NotFoundHttpException
     */
    public function actionSambaIndexing(): array {
        Yii::$app->request->enableCsrfValidation = false;
        $this->enableCsrfValidation = false;

        $file = Yii::$app->request->post('file');

        if (!empty($file)) {
            Yii::$app->queue->push(new SambaIndexingJob(['file' => $file]));

            Yii::$app->response->format = Response::FORMAT_JSON;

            return ['success' => true];
        }

        throw new NotFoundHttpException('File not found');
    }

    /**
     * @param string $path
     *
     * @return false|string
     */
    public function actionSambaDownload(string $path) {
        return file_get_contents($path);
    }
}

<?php

namespace consumer\components\services;

use consumer\components\jobs\LocalIndexingJob;
use Yii;
use yii\base\Component;
use yii\web\NotFoundHttpException;

class Local extends Component implements ServiceInterface {
    public $id;

    const CATEGORY_NAME = 'Загруженные файлы';

    /**
     * @param string $consumer
     *
     * @return void
     * @throws NotFoundHttpException
     */
    public function indexing(string $consumer = ''): void {
        if (empty($consumer)) {
            throw new NotFoundHttpException('Param consumer is required');
        }

        Yii::$app->queue->push(new LocalIndexingJob(['consumer' => $consumer]));
    }

    /**
     * @return bool
     */
    public function needAuth(): bool {
        return false;
    }
}
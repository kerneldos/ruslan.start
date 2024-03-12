<?php

namespace consumer\components\jobs;

use consumer\components\services\Local;
use consumer\models\Category;
use consumer\models\Document;
use consumer\models\File;
use consumer\models\QueueIndex;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\queue\JobInterface;

class LocalIndexingJob extends BaseObject implements JobInterface {
    protected int $rootCategoryId;

    public string $consumer;

    /**
     * @param $queue
     *
     * @return void
     * @throws InvalidConfigException
     */
    public function execute($queue) {
        $config = require_once dirname(__DIR__, 2) . '/config/client/' . $this->consumer . '.php';
        Yii::$app->set('db', $config['components']['db']);
        Yii::$app->params = array_merge(Yii::$app->params, $config['params']);

        $rootCategory = Category::findOne(['name' => Local::CATEGORY_NAME, 'parent_id' => 0]);
        if (empty($rootCategory)) {
            $rootCategory = new Category(['name' => Local::CATEGORY_NAME]);
            $rootCategory->save();
        }

        $this->rootCategoryId = $rootCategory->id;

        $this->processDocuments();

        /** @var File $file */
        foreach (File::find()->all() as $file) {
            $existsDocument = Document::findOne(['md5' => $file->size, 'type' => 'local']);

            if (empty($existsDocument)) {
                $documentToIndex = new QueueIndex([
                    'type' => 'local',
                    'data' => serialize($file),
                    'md5' => $file->size,
                ]);

                $documentToIndex->save(false);
            }
        }

        $this->processDocuments();
    }

    /**
     * @return void
     */
    protected function processDocuments() {
        $documentsToIndex = QueueIndex::find()->where(['type' => 'local'])->all();

        if (!empty($documentsToIndex)) {
            /** @var QueueIndex $queueItem */
            foreach ($documentsToIndex as $queueItem) {
                /** @var File $file */
                $file = unserialize($queueItem->data);

                $path = Yii::getAlias('@consumer/files/') . Yii::$app->params['sub_domain'] . '/' . $file->path;

                $document = new Document([
                    'name'       => $file->name,
                    'type'       => 'local',
                    'created'    => time(),
                    'mime_type'  => $file->type,
                    'media_type' => Document::getType($file->type),
                    'path'       => $path,
                    'sha256'     => $file->size,
                    'md5'        => $file->size,
                    'category'   => $this->rootCategoryId,
                    'file'       => $file->name,
                ]);

                Yii::$app->queue->push(new SambaFileJob(['document' => $document, 'consumer' => $this->consumer]));
            }
        }
    }
}
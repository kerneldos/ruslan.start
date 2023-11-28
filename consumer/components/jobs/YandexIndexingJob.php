<?php

namespace consumer\components\jobs;

use consumer\components\services\Yandex;
use consumer\models\Category;
use consumer\models\Document;
use Yii;
use yii\authclient\OAuth2;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class YandexIndexingJob extends BaseObject implements JobInterface {
    protected int $rootCategoryId;

    public string $consumer;

    /**
     * @param $queue
     *
     * @return void
     */
    public function execute($queue) {
        $rootCategory = Category::findOne(['name' => Yandex::CATEGORY_NAME, 'parent_id' => 0]);
        if (empty($rootCategory)) {
            $rootCategory = new Category(['name' => Yandex::CATEGORY_NAME]);
            $rootCategory->save();
        }

        $this->rootCategoryId = $rootCategory->id;

        /** @var OAuth2 $client */
        $client = Yii::$app->authClientCollection->getClient(Yandex::SERVICE_NAME);

        $response = $client->api('disk/resources/files', 'GET', [
            'limit' => 500,
            'offset' => 0,
        ]);

        if (!empty($response['items'])) {
            Yii::debug($response['items']);

            Document::$consumer = $this->consumer;

            foreach ($response['items'] as $file) {
                $downloadUrlResponse = $client->api('disk/resources/download', 'GET', ['path' => $file['path']]);

                if (!empty($downloadUrlResponse)) {
                    $document = new Document([
                        'name'       => $file['name'],
                        'type'       => 'yandex',
                        'created'    => $file['created'],
                        'mime_type'  => $file['mime_type'],
                        'media_type' => Document::getType($file['mime_type']),
                        'path'       => $downloadUrlResponse['href'],
                        'sha256'     => $file['sha256'],
                        'md5'        => $file['md5'],
                        'category'   => $this->rootCategoryId,
                        'file'       => $file['path'],
                    ]);

                    Yii::$app->queue->push(new SambaFileJob(['document' => $document, 'consumer' => $this->consumer]));
                }
            }
        } else {
            Yii::debug($response);
        }
    }
}
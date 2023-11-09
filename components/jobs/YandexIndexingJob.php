<?php

namespace app\components\jobs;

use app\components\services\Yandex;
use app\models\Category;
use app\models\Document;
use Yii;
use yii\authclient\OAuth2;
use yii\base\BaseObject;
use yii\httpclient\Exception;
use yii\queue\JobInterface;

class YandexIndexingJob extends BaseObject implements JobInterface {
    protected int $rootCategoryId;

    /**
     * @param $queue
     *
     * @return void
     * @throws Exception
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

            foreach ($response['items'] as $file) {
                if (empty(Document::findOne(['md5' => $file['md5']]))) {
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
                        ]);

                        Yii::$app->queue->push(new SambaFileJob(['document' => $document]));
                    }
                }
            }
        } else {
            Yii::debug($response);
        }
    }
}
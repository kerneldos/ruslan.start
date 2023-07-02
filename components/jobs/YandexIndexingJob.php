<?php

namespace app\components\jobs;

use app\models\Category;
use app\models\Config;
use app\models\Document;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\queue\JobInterface;

class YandexIndexingJob extends BaseObject implements JobInterface {
    protected int $rootCategoryId;

    const CATEGORY_NAME = 'Yandex Диск';

    /**
     * @param $queue
     *
     * @return void
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function execute($queue) {
        $rootCategory = Category::findOne(['name' => self::CATEGORY_NAME, 'parent_id' => 0]);
        if (empty($rootCategory)) {
            $rootCategory = new Category(['name' => self::CATEGORY_NAME]);
            $rootCategory->save();
        }

        $this->rootCategoryId = $rootCategory->id;

        $configToken = Config::findOne(['name' => 'yandex_api_token']);

        $client = new Client(['baseUrl' => 'https://cloud-api.yandex.net/v1/']);
        $response = $client->createRequest()
            ->addHeaders(['Authorization' => $configToken->value])
            ->setMethod('GET')
            ->setUrl('disk/resources/files')
            ->send();

        if ($response->isOk) {
            Yii::debug($response->data['items']);

            foreach ($response->data['items'] as $file) {
                if (empty(Document::findOne(['path' => $file['path']]))) {
                    $downloadUrlResponse = $client->createRequest()
                        ->addHeaders(['Authorization' => $configToken->value])
                        ->setUrl('disk/resources/download')
                        ->setMethod('GET')
                        ->setData(['path' => $file['path']])
                        ->send();

                    if ($downloadUrlResponse->isOk) {
                        $document = new Document([
                            'name'       => $file['name'],
                            'type'       => 'yandex',
                            'created'    => $file['created'],
                            'mime_type'  => $file['mime_type'],
                            'media_type' => $file['media_type'],
                            'path'       => $downloadUrlResponse->data['href'],
                            'sha256'     => $file['sha256'],
                            'md5'        => $file['md5'],
                            'category'   => $this->rootCategoryId,
                        ]);

                        Yii::$app->queue->push(new SambaFileJob(['document' => $document]));
                    }
                }
            }
        } else {
            Yii::debug($response->data);
        }
    }
}
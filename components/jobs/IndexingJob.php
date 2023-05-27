<?php

namespace app\components\jobs;

use app\helpers\FileConverter;
use app\models\Config;
use app\models\Document;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\queue\JobInterface;

class IndexingJob extends BaseObject implements JobInterface {

    /**
     * @param $queue
     *
     * @return void
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function execute($queue) {
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

                    $content = '';
                    if ($downloadUrlResponse->isOk) {
                        $fileName = Yii::getAlias('@runtime/tempFile.data');
                        $fileData = file_get_contents($downloadUrlResponse->data['href']);
                        file_put_contents($fileName, $fileData);

                        $converter = new FileConverter($fileName);
                        $content   = $converter->convert($file['mime_type']);
                        @unlink($fileName);
                    }

                    $document = new Document([
                        'name'       => $file['name'],
                        'content'    => $content,
                        'created'    => $file['created'],
                        'mime_type'  => $file['mime_type'],
                        'media_type' => $file['media_type'],
                        'path'       => $file['path'],
                        'sha256'     => $file['sha256'],
                        'md5'        => $file['md5'],
                    ]);
                    $document->save();
                } else {
                    $document = Document::findOne(['path' => $file['path']]);

                    if ($document->md5 !== $file['md5']) {
                        $downloadUrlResponse = $client->createRequest()
                            ->addHeaders(['Authorization' => $configToken->value])
                            ->setUrl('disk/resources/download')
                            ->setMethod('GET')
                            ->setData(['path' => $file['path']])
                            ->send();

                        $content = '';
                        if ($downloadUrlResponse->isOk) {
                            if (in_array($file['mime_type'], FileConverter::AVAILABLE_MIME_TYPES)) {
                                $fileName = Yii::getAlias('@runtime/tempFile.data');
                                $fileData = file_get_contents($downloadUrlResponse->data['href']);
                                file_put_contents($fileName, $fileData);

                                $converter = new FileConverter($fileName);
                                $content   = $converter->convert($file['mime_type']);
                                @unlink($fileName);
                            }
                        }

                        $document->content = $content;
                        $document->save();
                    }
                }
            }
        }
    }
}
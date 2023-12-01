<?php

namespace consumer\components\jobs;

use consumer\components\TikaClient;
use consumer\models\AiCategory;
use consumer\models\Document;
use consumer\models\Tag;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\queue\JobInterface;

class SambaFileJob extends BaseObject implements JobInterface {
    public Document $document;

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

        $existsDocument = Document::findOne(['md5' => $this->document->md5, 'type' => $this->document->type]);

        if (empty($existsDocument)) {
            if ($this->document->size < 200 * 1024 * 1024) {
                try {
                    $client = TikaClient::make('tika.local', 9998);
                    $client->setTimeout(300);
                    $client->setOCRLanguages(['rus', 'eng']);

                    $content = $client->getText($this->document->path);
                } catch (\Throwable $exception) {
                    $content = 'Error get text';
                }
            } else {
                $content = 'Very Large File';
            }

            $this->document->content = $content;

//            $documentTags = [];
//
//            $tags = Tag::find()->all();
//            if (!empty($tags)) {
//                /** @var Tag $tag */
//                foreach ($tags as $tag) {
//                    $documentTag = Document::find()->query([
//                        'bool' => [
//                            'must' => [
//                                'simple_query_string' => [
//                                    'fields' => [
//                                        'name^2',
//                                        'attachment.content',
//                                    ],
//                                    'query' => $tag->name,
//                                ],
//                            ],
//                            'filter' => [
//                                'term' => ['_id' => $this->document->_id],
//                            ],
//                        ],
//                    ])->one();
//
//                    if (!empty($documentTag)) {
//                        $documentTags[] = $tag->name;
//                    }
//                }
//
//                $this->document->tags = $documentTags;
//            }

            try {
                $client = new Client([
                    'requestConfig' => [
                        'format' => Client::FORMAT_JSON,
                    ],
                    'baseUrl' => 'http://ai/',
                ]);

                $request = $client->post('get_category', ['content' => base64_encode($content)]);

                $response = $request->send();
                if (!empty($response->data['category'])) {
                    $aiCategory = AiCategory::findOne(['name' => $response->data['category']]);
                    if (empty($aiCategory)) {
                        $aiCategory = new AiCategory(['name' => $response->data['category']]);
                        $aiCategory->save();
                    }

                    $aiCategoryId = $aiCategory->id;
                    if (!empty($response->data['subcategory'])) {
                        $subCategory = AiCategory::findOne([
                            'name' => $response->data['subcategory'],
                            'parent_id' => $aiCategory->id,
                        ]);

                        if (empty($subCategory)) {
                            $subCategory = new AiCategory([
                                'name' => $response->data['subcategory'],
                                'parent_id' => $aiCategory->id,
                            ]);
                            $subCategory->save();
                        }

                        $aiCategoryId = $subCategory->id;
                    }

                    $this->document->ai_category = $aiCategoryId;
                }
            } catch (\Throwable $exception) {
                file_put_contents(Yii::getAlias('/tmp/insert.log'), print_r($exception->getMessage(), true), FILE_APPEND);
            }

            $this->document->save();
        }
    }
}
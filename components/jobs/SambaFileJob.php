<?php

namespace app\components\jobs;

use app\helpers\FileConverter;
use app\models\AiCategory;
use app\models\AiTextCategory;
use app\models\Document;
use app\models\Tag;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\queue\JobInterface;
use Vaites\ApacheTika\Client as TikaClient;

class SambaFileJob extends BaseObject implements JobInterface {
    public Document $document;

    /**
     * @param $queue
     *
     * @return void
     */
    public function execute($queue) {
        $existsDocument = Document::findOne(['md5' => $this->document->md5, 'type' => $this->document->type]);

        $content = '';
        if (empty($existsDocument)) {
            if ($this->document->size < 20 * 1024 * 1024) {
                try {
                    $client = TikaClient::make('tika.local', 9998);
                    $client->setTimeout(300);
                    $client->setOCRLanguages(['rus', 'eng']);

                    $content = $client->getText($this->document->path);
                } catch (\Throwable $exception) {
                    $content = 'Error get text';
                }
            }

            $this->document->content = $content;
            $this->document->save();
        }

        try {
            $this->document = Document::findOne($this->document->_id);

            $documentTags = [];

            $tags = Tag::find()->all();
            if (!empty($tags)) {
                /** @var Tag $tag */
                foreach ($tags as $tag) {
                    $documentTag = Document::find()->query([
                        'bool' => [
                            'must' => [
                                'simple_query_string' => [
                                    'fields' => [
                                        'name^2',
                                        'attachment.content',
                                    ],
                                    'query' => $tag->name,
                                ],
                            ],
                            'filter' => [
                                'term' => ['_id' => $this->document->_id],
                            ],
                        ],
                    ])->one();

                    if (!empty($documentTag)) {
                        $documentTags[] = $tag->name;
                    }
                }

                $this->document->tags = $documentTags;
            }

            $client = new Client([
                'requestConfig' => [
                    'format' => Client::FORMAT_JSON,
                ],
                'baseUrl' => 'http://ai/',
            ]);

            $request = $client->post('get_category', ['content' => $content]);

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
                $this->document->save();
            }
        } catch (Throwable $exception) {
            file_put_contents(Yii::getAlias('@runtime/logs/insert.log'), print_r($exception->getMessage(), true), FILE_APPEND);
        }
    }
}
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
                    $converter = new FileConverter(['document' => $this->document]);

                    $content = $converter->convert();
                } catch (Throwable $exception) {
                    file_put_contents(Yii::getAlias('@runtime/logs/scan.log'), print_r($exception->getMessage(), true), FILE_APPEND);
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

            $hash = md5($content);
            $aiTextCategory = AiTextCategory::findOne(['hash' => $hash]);
            if (empty($aiTextCategory)) {
                $client = new Client([
                    'requestConfig' => [
                        'format' => Client::FORMAT_JSON,
                    ],
                    'baseUrl' => 'http://ai/',
                ]);

                $request = $client->post('get_category', ['content' => $content]);

                $response = $request->send();

                $aiTextCategory = new AiTextCategory([
                    'hash' => $hash,
                    'name' => $response->data['category'] ?? '',
                ]);
                $aiTextCategory->save();
            }

            $this->document->ai_category = $aiTextCategory->id;

            $this->document->save();
        } catch (Throwable $exception) {
            file_put_contents(Yii::getAlias('@runtime/logs/insert.log'), print_r($exception->getMessage(), true), FILE_APPEND);
        }
    }
}
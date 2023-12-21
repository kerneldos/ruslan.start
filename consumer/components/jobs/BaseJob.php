<?php

namespace consumer\components\jobs;

use consumer\components\TikaClient;
use consumer\models\AiCategory;
use consumer\models\Document;
use Yii;
use yii\base\BaseObject;
use yii\httpclient\Client;

class BaseJob extends BaseObject {
    /**
     * @param Document $document
     *
     * @return void
     */
    public function indexDocument(Document $document) {
        $existsDocument = Document::findOne(['md5' => $document->md5, 'type' => $document->type]);

        if (empty($existsDocument)) {
            if ($document->size < 200 * 1024 * 1024) {
                try {
                    $client = TikaClient::make('tika.local', 9998);
                    $client->setTimeout(300);
                    $client->setOCRLanguages(['rus', 'eng']);

                    $content = $client->getText($document->path);
                } catch (\Throwable $exception) {
                    $content = 'Error get text';
                }
            } else {
                $content = 'Very Large File';
            }

            $document->content = $content;

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
//                                'term' => ['_id' => $document->_id],
//                            ],
//                        ],
//                    ])->one();
//
//                    if (!empty($documentTag)) {
//                        $documentTags[] = $tag->name;
//                    }
//                }
//
//                $document->tags = $documentTags;
//            }

            try {
                $client = new Client([
                    'requestConfig' => [
                        'format' => Client::FORMAT_JSON,
                    ],
                    'baseUrl' => 'http://ai/',
                ]);

                $request = $client->post('get_category', [
                    'content' => base64_encode($content),
                    'filename' => $document->name,
                ]);

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

                    $document->ai_category = $aiCategoryId;
                }
            } catch (\Throwable $exception) {
                file_put_contents(Yii::getAlias('/tmp/insert.log'), print_r($exception->getMessage(), true), FILE_APPEND);
            }

            $document->save();
        }
    }
}
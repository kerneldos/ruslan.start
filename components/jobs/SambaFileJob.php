<?php

namespace app\components\jobs;

use app\helpers\FileConverter;
use app\models\Document;
use app\models\Tag;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SambaFileJob extends BaseObject implements JobInterface {
    public Document $document;

    /**
     * @param $queue
     *
     * @return void
     */
    public function execute($queue) {
        $content = '';
        if ($this->document->size < 20 * 1024 * 1024) {
            try {
                $converter = new FileConverter(['document' => $this->document]);

                $content = $converter->convert();
            } catch (Throwable $exception) {
                file_put_contents(Yii::getAlias('@runtime/logs/scan.log'), print_r($exception->getMessage(), true), FILE_APPEND);
            }
        }

        try {
            $hash = md5(join('', [$this->document->created, $this->document->size]));

            $this->document->content = $content;
            $this->document->sha256  = $hash;
            $this->document->md5     = $hash;
            $this->document->save();

            $documentTags = [];

            $tags = Tag::find()->all();
            if (!empty($tags)) {
                /** @var Tag $tag */
                foreach ($tags as $tag) {
                    $documentTag = Document::find()->query([
                        'bool' => [
                            'must' => [
                                [
                                    'term' => ['_id' => $this->document->_id],
                                ],
                                [
                                    'simple_query_string' => [
                                        'fields' => [
                                            'name^2',
                                            'attachment.content',
                                        ],
                                        'query' => sprintf('*%s*', $tag->name),
                                        'default_operator' => 'or',
                                        'analyze_wildcard' => true,
                                        'minimum_should_match' => '-35%',
                                    ],
                                ],
                            ]
                        ],
                    ])->one();

                    if (!empty($documentTag)) {
                        $documentTags[] = $tag->name;
                    }
                }

                $this->document->tags = $documentTags;
                $this->document->save();
            }
        } catch (Throwable $exception) {
            file_put_contents(Yii::getAlias('@runtime/logs/insert.log'), print_r($exception->getMessage(), true), FILE_APPEND);
        }
    }
}
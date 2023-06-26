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

            $es = Yii::$app->elasticsearch;

            $response = $es->post('documents/_termvectors', [], json_encode([
                '_id' => $this->document->_id,
                'fields' => ['attachment.content'],
                'term_statistics' => true,
                'field_statistics' => true,
                'positions' => false,
                'offsets' => false,
                'filter' => [
                    'min_term_freq' => 25,
                    'min_doc_freq' => 1,
                    'min_word_length' => 4
                ]
            ]));

            foreach ($response['term_vectors']['attachment.content']['terms'] ?? [] as $term => $item) {
                $tag = Tag::findOne(['name' => $term]);

                if (empty($tag)) {
                    $tag = new Tag(['name' => $term]);
                    $tag->save();
                }
            }
        } catch (Throwable $exception) {
            file_put_contents(Yii::getAlias('@runtime/logs/insert.log'), print_r($exception->getMessage(), true), FILE_APPEND);
        }
    }
}
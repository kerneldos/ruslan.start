<?php

namespace app\components\jobs;

use app\helpers\FileConverter;
use app\models\Document;
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
        } catch (Throwable $exception) {
            file_put_contents(Yii::getAlias('@runtime/logs/insert.log'), print_r($exception->getMessage(), true), FILE_APPEND);
        }
    }
}
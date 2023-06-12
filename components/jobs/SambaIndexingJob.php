<?php

namespace app\components\jobs;

use app\helpers\FileConverter;
use app\models\Document;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SambaIndexingJob extends BaseObject implements JobInterface {
    public array $file;

    /**
     * @param $queue
     *
     * @return void
     */
    public function execute($queue) {
        $file = $this->file;

        if (empty(Document::findOne(['path' => $file['path']]))) {
//            $content = '';
//            if (in_array($file['mime_type'], FileConverter::AVAILABLE_MIME_TYPES)) {
//                $fileName = Yii::getAlias('@runtime/' . $file['name']);
//                file_put_contents($fileName, base64_decode($file['content']));
//
//                $converter = new FileConverter($fileName);
//                $content   = $converter->convert($file['mime_type']);
//                @unlink($fileName);
//            }

            $fields = [
                'type'       => 'samba',
                'name'       => $file['name'],
                'content'    => $file['content'],
                'created'    => $file['ctime'],
                'mime_type'  => $file['mime_type'],
                'media_type' => $file['mime_type'],
                'path'       => $file['path'],
                'sha256'     => $file['hash'],
                'md5'        => $file['hash'],
            ];
            $document = new Document($fields);
            $document->insert(true, array_keys($fields), ['pipeline' => 'attachment']);
        } else {
            $document = Document::findOne(['path' => $file['path']]);

            if ($document->md5 !== $file['hash']) {
                $content = '';
                if (in_array($file['mime_type'], FileConverter::AVAILABLE_MIME_TYPES)) {
                    $fileName = Yii::getAlias('@runtime/tempFile.data');
                    file_put_contents($fileName, $file['content']);

                    $converter = new FileConverter($fileName);
                    $content   = $converter->convert($file['mime_type']);
                    @unlink($fileName);
                }

                $document->content = $content;
                $document->save();
            }
        }
    }
}
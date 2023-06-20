<?php

namespace app\components\jobs;

use app\helpers\FileConverter;
use app\models\Document;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SambaFileJob extends BaseObject implements JobInterface {
    public string $host = '10.8.0.6';
    public string $user;
    public string $password;
    public array $file;

    /**
     * @param $queue
     *
     * @return void
     */
    public function execute($queue) {
        $file = $this->file;

        $fullPath = $file['path'];

        $document = Document::findOne(['path' => $fullPath]);
        if (empty($document)) {
            $mimeType = $file['mime_type'];

            $content = '';
            if ($file['size'] < 20 * 1024 * 1024) {
                try {
                    $content = $this->getFileContent([
                        'content' => base64_encode(file_get_contents($fullPath)),
                        'mime_type' => $mimeType,
                    ]);
                } catch (\Throwable $exception) {
                    file_put_contents(Yii::getAlias('@runtime/logs/scan.log'), print_r($exception->getTraceAsString(), true), FILE_APPEND);
                }
            }

            $fields = [
                'type'       => 'samba',
                'name'       => $file['name'],
                'content'    => $content,
                'created'    => $file['ctime'],
                'mime_type'  => $file['mime_type'],
                'media_type' => $file['mime_type'],
                'path'       => $fullPath,
                'sha256'     => md5($content),
                'md5'        => md5($content),
            ];

            try {
                $document = new Document($fields);
                $document->save();
            } catch (\Throwable $exception) {
                file_put_contents(Yii::getAlias('@runtime/logs/insert.log'), print_r($exception->getMessage(), true), FILE_APPEND);
            }
        } else {
//                        if ($document->md5 !== $file['hash']) {
//                            $document->content = $this->getFileContent($file);
//                            $document->update(true, ['content'], ['pipeline' => 'attachment']);
//                        }
        }
    }

    /**
     * @param array $file
     *
     * @return string
     */
    protected function getFileContent(array $file): string {
        $converter = new FileConverter(['file' => $file]);

        return $converter->convert($file['mime_type']);
    }
}
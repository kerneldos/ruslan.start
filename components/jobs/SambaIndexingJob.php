<?php

namespace app\components\jobs;

use app\helpers\FileConverter;
use app\models\Document;
use Yii;
use yii\base\BaseObject;
use yii\db\StaleObjectException;
use yii\elasticsearch\Exception;
use yii\queue\JobInterface;

class SambaIndexingJob extends BaseObject implements JobInterface {
    /**
     * @var array $file
     */
    public array $file;

    /**
     * @param $queue
     *
     * @return void
     * @throws \yii\db\Exception
     * @throws StaleObjectException
     * @throws Exception
     */
    public function execute($queue) {
        $file = $this->file;

        $document = Document::findOne(['path' => $file['path']]);
        if (empty($document)) {
            $content = $this->getFileContent($file);

            $fields = [
                'type'       => 'samba',
                'name'       => $file['name'],
                'content'    => $content,
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
            if ($document->md5 !== $file['hash']) {
                $document->content = $this->getFileContent($file);
                $document->update(true, ['content'], ['pipeline' => 'attachment']);
            }
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
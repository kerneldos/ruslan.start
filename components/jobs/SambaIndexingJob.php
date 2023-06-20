<?php

namespace app\components\jobs;

use app\models\Document;
use Yii;
use yii\base\BaseObject;
use yii\db\StaleObjectException;
use yii\elasticsearch\Exception;
use yii\queue\JobInterface;

class SambaIndexingJob extends BaseObject implements JobInterface {
    public string $host = '10.8.0.6';
    public string $user;
    public string $password;

    /**
     * @param $queue
     *
     * @return void
     * @throws \yii\db\Exception
     * @throws StaleObjectException
     * @throws Exception
     */
    public function execute($queue) {
        $this->scanFiles();
    }

    /**
     * @param string $remotePath
     *
     * @return void
     * @throws Exception
     * @throws StaleObjectException
     * @throws \yii\db\Exception
     */
    protected function scanFiles(string $remotePath = ''): void {
        $dir = sprintf('smb://%s:%s@%s%s', $this->user, $this->password, $this->host, $remotePath);

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            if (strpos($file, '$') === false) {
                $path = join(DIRECTORY_SEPARATOR, [$remotePath, $file]);
                $fullPath = join(DIRECTORY_SEPARATOR, [$dir, $file]);

                if (is_dir($fullPath)) {
                    $this->scanFiles($path);
                } else {
                    $fileInfo = stat($fullPath);

                    $document = Document::findOne(['path' => $fullPath]);
                    if (empty($document)) {
                        $mimeType = mime_content_type($fullPath);

                        $fields = [
                            'type'       => 'samba',
                            'name'       => $file,
                            'created'    => $fileInfo['ctime'],
                            'mime_type'  => $mimeType,
                            'media_type' => $mimeType,
                            'path'       => $fullPath,
                            'size'       => $fileInfo['size'],
                        ];

                        Yii::$app->queue->push(new SambaFileJob([
                            'user' => $this->user,
                            'password' => $this->password,
                            'file' => $fields,
                        ]));
                    } else {
//                        if ($document->md5 !== $file['hash']) {
//                            $document->content = $this->getFileContent($file);
//                            $document->update(true, ['content'], ['pipeline' => 'attachment']);
//                        }
                    }
                }
            }
        }
    }
}

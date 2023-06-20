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

                        $content = '';
                        if ($fileInfo['size'] < 20 * 1024 * 1024) {
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
                            'name'       => $file,
                            'content'    => $content,
                            'created'    => $fileInfo['ctime'],
                            'mime_type'  => $mimeType,
                            'media_type' => $mimeType,
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
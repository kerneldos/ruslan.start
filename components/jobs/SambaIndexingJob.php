<?php

namespace app\components\jobs;

use app\models\Document;
use Yii;
use yii\base\BaseObject;
use yii\db\StaleObjectException;
use yii\elasticsearch\Exception;
use yii\queue\JobInterface;
use ZipArchive;

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
        $this->scanRemote();
    }

    /**
     * @param string $remotePath
     *
     * @return void
     * @throws Exception
     * @throws StaleObjectException
     * @throws \yii\db\Exception
     */
    protected function scanRemote(string $remotePath = ''): void {
        $dir = sprintf('smb://%s:%s@%s%s', $this->user, $this->password, $this->host, $remotePath);

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            if (strpos($file, '$') === false) {
                $path = join(DIRECTORY_SEPARATOR, [$remotePath, $file]);
                $fullPath = join(DIRECTORY_SEPARATOR, [$dir, $file]);

                if (is_dir($fullPath)) {
                    $this->scanRemote($path);
                } else {
                    $fileInfo = stat($fullPath);
                    $mimeType = mime_content_type($fullPath);

                    $document = Document::findOne(['path' => $fullPath]);

                    if (!$this->isArchive($mimeType)) {
                        if (empty($document)) {
                            $document = new Document([
                                'type'       => 'samba',
                                'name'       => $file,
                                'created'    => $fileInfo['ctime'],
                                'mime_type'  => $mimeType,
                                'media_type' => $mimeType,
                                'path'       => $fullPath,
                                'size'       => $fileInfo['size'],
                            ]);

                            Yii::$app->queue->push(new SambaFileJob(['document' => $document]));
                        } else {
//                            if ($document->md5 !== $file['hash']) {
//                                $document->content = $this->getFileContent($file);
//                                $document->update(true, ['content'], ['pipeline' => 'attachment']);
//                            }
                        }
                    } else {
                        $this->extractArchive($fullPath, $mimeType);
                    }
                }
            }
        }
    }

    /**
     * @param string $mimeType
     *
     * @return bool
     */
    protected function isArchive(string $mimeType): bool {
        return in_array($mimeType, [
            'application/zip',
            'application/x-rar',
            'application/x-7z-compressed',
        ]);
    }

    /**
     * @param string $filePath
     * @param string $mimeType
     *
     * @return void
     */
    protected function extractArchive(string $filePath, string $mimeType): void {
        switch ($mimeType) {
            case 'application/zip':
                $this->extractZip($filePath);
                break;

            case 'application/x-rar':
                $this->extractRar($filePath);
                break;
        }
    }

    /**
     * @param string $filePath
     *
     * @return void
     */
    protected function extractZip(string $filePath): void {
        try {
            $fileInfo = stat($filePath);

            if ($fileInfo['size'] < 20 * 1024 * 1024) {
                $fileName = Yii::getAlias('@runtime/' . basename($filePath) . '.zip');

                if (copy($filePath, $fileName)) {
                    $zip = new ZipArchive;

                    if ($zip->open($fileName) === true) {
                        $dir = Yii::getAlias('@runtime/' . basename($filePath));

                        $zip->extractTo($dir);
                        $zip->close();

                        $this->scanDir($dir);
                    }
                }
            }
        } catch (\Throwable $exception) {
            Yii::error($exception->getMessage());
        }
    }

    /**
     * @param string $filePath
     *
     * @return void
     */
    protected function extractRar(string $filePath): void {

    }

    /**
     * @param string $path
     *
     * @return void
     */
    protected function scanDir(string $path): void {
        $files = array_diff(scandir($path), ['.', '..']);

        foreach ($files as $file) {
            $fullPath = join(DIRECTORY_SEPARATOR, [$path, $file]);

            if (is_dir($fullPath)) {
                $this->scanDir($fullPath);
            } else {
                $fileInfo = stat($fullPath);
                $mimeType = mime_content_type($fullPath);

                $document = Document::findOne(['path' => $fullPath]);

                if (!$this->isArchive($mimeType)) {
                    if (empty($document)) {
                        $document = new Document([
                            'type'       => 'samba',
                            'name'       => $file,
                            'created'    => $fileInfo['ctime'],
                            'mime_type'  => $mimeType,
                            'media_type' => $mimeType,
                            'path'       => $fullPath,
                            'size'       => $fileInfo['size'],
                        ]);

                        Yii::$app->queue->push(new SambaFileJob(['document' => $document]));
                    } else {
                        //                            if ($document->md5 !== $file['hash']) {
                        //                                $document->content = $this->getFileContent($file);
                        //                                $document->update(true, ['content'], ['pipeline' => 'attachment']);
                        //                            }
                    }
                } else {
                    $this->extractArchive($fullPath, $mimeType);
                }
            }
        }
    }
}

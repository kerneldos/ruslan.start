<?php

namespace app\components\jobs;

use app\models\Category;
use app\models\Document;
use RarArchive;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use ZipArchive;

class SambaIndexingJob extends BaseObject implements JobInterface {
    public string $host = '10.8.0.6';
    public string $user;
    public string $password;

    public string $tempDir;

    protected int $rootCategoryId;

    const CATEGORY_NAME = 'Локальные файлы';

    /**
     * @param $queue
     *
     * @return void
     */
    public function execute($queue) {
        $this->tempDir = Yii::getAlias('@runtime/temp/');

        $rootCategory = Category::findOne(['name' => self::CATEGORY_NAME, 'parent_id' => 0]);
        if (empty($rootCategory)) {
            $rootCategory = new Category(['name' => self::CATEGORY_NAME]);
            $rootCategory->save();
        }

        $this->rootCategoryId = $rootCategory->id;

        $this->scanRemote('', $this->rootCategoryId);
    }

    /**
     * @param string $remotePath
     * @param int    $categoryId
     *
     * @return void
     */
    protected function scanRemote(string $remotePath = '', int $categoryId = 0): void {
        $dir = sprintf('smb://%s:%s@%s%s', $this->user, $this->password, $this->host, $remotePath);

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            if (strpos($file, '$') === false) {
                $path = join(DIRECTORY_SEPARATOR, [$remotePath, $file]);
                $fullPath = join(DIRECTORY_SEPARATOR, [$dir, $file]);

                if (is_dir($fullPath)) {
                    $category = $this->generateCategory($categoryId, $file);

                    $this->scanRemote($path, $category->id);
                } else {
                    $this->processFile($fullPath, $file, $categoryId);
                }
            }
        }
    }

    /**
     * @param int    $parentId
     * @param string $name
     *
     * @return Category
     */
    public function generateCategory(int $parentId, string $name): Category {
        $folderCategory = Category::findOne(['name' => $name, 'parent_id' => $parentId]);

        if (empty($folderCategory)) {
            $folderCategory = new Category(['name' => $name, 'parent_id' => $parentId]);
            $folderCategory->save();
        }

        return $folderCategory;
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
                $fileName = $this->tempDir . basename($filePath);

                if (copy($filePath, $fileName)) {
                    $zip = new ZipArchive;

                    if ($zip->open($fileName) === true) {
                        $dir = $this->tempDir . basename($filePath) . '_dir';

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
        try {
            $fileInfo = stat($filePath);

            if ($fileInfo['size'] < 20 * 1024 * 1024) {
                $fileName = $this->tempDir . basename($filePath);

                if (copy($filePath, $fileName)) {
                    if (($rar = RarArchive::open($fileName)) !== false) {
                        $dir = $this->tempDir . basename($filePath) . '_dir';

                        $entries = $rar->getEntries() ?? [];
                        foreach ($entries as $entry) {
                            $entry->extract($dir);
                        }

                        $rar->close();

                        $this->scanDir($dir);
                    }
                }
            }
        } catch (\Throwable $exception) {
            Yii::error($exception->getMessage());
        }
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
                $this->processFile($fullPath, $file, $this->rootCategoryId);
            }
        }
    }

    /**
     * @param string $fullPath
     * @param        $fileName
     * @param int    $categoryId
     *
     * @return void
     */
    protected function processFile(string $fullPath, $fileName, int $categoryId): void {
        $fileInfo = stat($fullPath);
        $mimeType = mime_content_type($fullPath);
        $hash     = md5(join('', [$fileInfo['ctime'], $fileInfo['size']]));

        $document = Document::findOne(['path' => $fullPath]);
        $needIndex = empty($document) || $document->md5 !== $hash;

        if (empty($document)) {
            $document = new Document([
                'type' => 'samba',
                'name' => $fileName,
                'created' => $fileInfo['ctime'],
                'mime_type' => $mimeType,
                'media_type' => $mimeType,
                'path' => $fullPath,
                'size' => $fileInfo['size'],
                'sha256' => $hash,
                'md5' => $hash,
                'category' => $categoryId,
            ]);
        }

        if ($needIndex) {
            if (!$this->isArchive($mimeType)) {
                Yii::$app->queue->push(new SambaFileJob(['document' => $document]));
            } else {
                $document->content = '';
                $document->save();

                $this->extractArchive($fullPath, $mimeType);
            }
        }
    }
}

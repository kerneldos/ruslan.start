<?php

namespace app\components\jobs;

use app\components\services\Bitrix;
use app\models\Category;
use app\models\Document;
use Yii;
use yii\authclient\ClientInterface;
use yii\authclient\OAuth2;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class BitrixIndexingJob extends BaseObject implements JobInterface {
    protected int $rootCategoryId;

    /** @var OAuth2|ClientInterface $client */
    protected $client;

    /**
     * @param $queue
     *
     * @return void
     */
    public function execute($queue) {
        $rootCategory = Category::findOne(['name' => Bitrix::CATEGORY_NAME, 'parent_id' => 0]);
        if (empty($rootCategory)) {
            $rootCategory = new Category(['name' => Bitrix::CATEGORY_NAME]);
            $rootCategory->save();
        }

        $this->rootCategoryId = $rootCategory->id;

        $this->client = Yii::$app->authClientCollection->getClient(Bitrix::SERVICE_NAME);

        $this->importFiles();
    }

    /**
     * @return array
     */
    public function getStorages(): array {
        $response = $this->client->api('disk.storage.getlist');

        return $response['result'] ?? [];
    }

    /**
     * @param int $storageId
     *
     * @return array
     */
    public function getRootChildren(int $storageId): array {
        $response = $this->client->api('disk.storage.getchildren', 'GET', ['id' => $storageId]);

        return $response['result'] ?? [];
    }

    /**
     * @param string $path
     * @param int    $folderId
     *
     * @return void
     */
    public function getFolderChildren(string $path, int $folderId): void {
        $response = $this->client->api('disk.folder.getchildren', 'GET', ['id' => $folderId]);

        $children = $response['result'] ?? [];

        if (!empty($children)) {
            foreach ($children as $child) {
                if ($child['TYPE'] == 'folder') {
                    $this->getFolderChildren(join(DIRECTORY_SEPARATOR, [$path, $child['NAME']]), $child['ID']);
                } else {
                    $hash = md5(join('', [$child['CREATE_TIME'], $child['SIZE']]));

                    $document = new Document([
                        'name'       => $child['NAME'],
                        'type'       => 'bitrix',
                        'created'    => $child['CREATE_TIME'],
                        'mime_type'  => $child['TYPE'],
                        'media_type' => Document::getType($child['TYPE']),
                        'path'       => $child['DOWNLOAD_URL'] ?? $child['DETAIL_URL'],
                        'sha256'     => $hash,
                        'md5'        => $hash,
                        'category'   => $this->rootCategoryId,
                    ]);

                    Yii::$app->queue->push(new SambaFileJob(['document' => $document]));
                }
            }
        }
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function importFiles(string $path = ''): void {
        $storages = $this->getStorages();
        file_put_contents(Yii::getAlias('@runtime/storages.log'), print_r($storages, true), FILE_APPEND);

        foreach ($storages as $storage) {
            $children = $this->getRootChildren($storage['ID']);

            if (!empty($children)) {
                foreach ($children as $child) {
                    if ($child['TYPE'] == 'folder') {
                        $this->getFolderChildren(join(DIRECTORY_SEPARATOR, [$path, $storage['NAME'], $child['NAME']]), $child['ID']);
                    } else {
                        $hash = md5(join('', [$child['CREATE_TIME'], $child['SIZE']]));

                        $document = new Document([
                            'name'       => $child['NAME'],
                            'type'       => 'bitrix',
                            'created'    => $child['CREATE_TIME'],
                            'mime_type'  => $child['TYPE'],
                            'media_type' => Document::getType($child['TYPE']),
                            'path'       => $child['DOWNLOAD_URL'] ?? $child['DETAIL_URL'],
                            'sha256'     => $hash,
                            'md5'        => $hash,
                            'category'   => $this->rootCategoryId,
                        ]);

                        Yii::$app->queue->push(new SambaFileJob(['document' => $document]));
                    }
                }
            }
        }
    }
}
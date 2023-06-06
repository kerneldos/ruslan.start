<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\httpclient\Client;
use yii\httpclient\Exception;

class SambaIndexingController extends Controller {
    protected Client $httpClient;

    /**
     * @return int
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionIndex(): int {
        $this->httpClient = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
            'baseUrl' => 'https://10.8.0.1/',
        ]);

        $this->scanFiles(\Yii::getAlias('@app/files'));

        return ExitCode::OK;
    }

    /**
     * @param string $remotePath
     *
     * @return void
     * @throws InvalidConfigException
     * @throws Exception
     */
    protected function scanFiles(string $remotePath = ''): void {
        $files = scandir($remotePath);

        foreach (array_diff($files, ['.', '..']) as $file) {
            $path = join(DIRECTORY_SEPARATOR, [$remotePath, $file]);

            if (is_dir($path)) {
                $this->scanFiles($path);
            } else {
                echo 'Process file: ' . $path . PHP_EOL;

                $fileInfo = stat($path);
                if ($fileInfo['size'] < 2 * 1024 * 1024) {
                    $name = (strlen($file) <= 1000) ? $file : mb_substr($file, 0, 999);
                    $hash = md5(join(':', [$fileInfo['size'], $fileInfo['mtime'], $path]));

                    $data = [
                        'href'      => $path,
                        'hash'      => $hash,
                        'id'        => $name,
                        'name'      => $file,
                        'path'      => $path,
                        'size'      => $fileInfo['size'],
                        'mtime'     => $fileInfo['mtime'],
                        'ctime'     => $fileInfo['ctime'],
                        'atime'     => $fileInfo['atime'],
                        'uid'       => $fileInfo['uid'],
                        'gid'       => $fileInfo['gid'],
                        'mime_type' => mime_content_type($path),
                        'content'   => base64_encode(file_get_contents($path)),
                    ];

                    $response = $this->httpClient->createRequest()
                        ->setMethod('POST')
                        ->setUrl('api/samba-indexing')
                        ->setOptions([
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                        ])
                        ->setData(['file' => $data])
                        ->send();

                    if ($response->isOk) {
                        echo sprintf('File %s send to server%s', $file, PHP_EOL);
                    } else {
                        echo $response->content;
                    }
                } else {
                    echo $fileInfo['size'] . PHP_EOL;
                }
            }
        }
    }
}

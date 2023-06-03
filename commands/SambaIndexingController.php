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

/**
 * @property Client $httpClient
 */
class SambaIndexingController extends Controller
{
    /**
     * @return int
     */
    public function actionIndex(): int {
        $this->httpClient = new Client([
            'baseUrl' => 'https://10.8.0.1/',
        ]);

        $this->scanFiles();

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
        $dir = sprintf('smb://%s:%s@%s%s', 'guest', 'kernel32', '192.168.0.102', $remotePath);

        $files = scandir($dir);
        if (!empty($files)) {
            foreach (array_diff($files, ['.', '..', '$']) as $file) {
                if (stripos($file, '$') === false) {
                    $path = join(DIRECTORY_SEPARATOR, [$remotePath, $file]);
                    $fullPath = join(DIRECTORY_SEPARATOR, [$dir, $file]);

                    if (is_dir($fullPath)) {
                        $this->scanFiles($path);
                    } else {
                        $fileInfo = stat($fullPath);
                        $name = (strlen($file) <= 1000) ? $file : mb_substr($file, 0, 999);
                        $hash = md5(join(':', [$fileInfo['size'], $fileInfo['mtime'], $path]));

                        $data = [
                            'href'      => $path,
                            'hash'      => $hash,
                            'id'        => $name,
                            'name'      => $file,
                            'path'      => $fullPath,
                            'size'      => $fileInfo['size'],
                            'mtime'     => $fileInfo['mtime'],
                            'ctime'     => $fileInfo['ctime'],
                            'atime'     => $fileInfo['atime'],
                            'uid'       => $fileInfo['uid'],
                            'gid'       => $fileInfo['gid'],
                            'mime_type' => mime_content_type($fullPath),
                            'content'   => file_get_contents($fullPath),
                        ];

                        $response = $this->httpClient->createRequest()
                            ->setMethod('POST')
                            ->setUrl('api/samba-indexing')
                            ->setOptions([
                                'ssl' => [
                                    'verify_peer' => false,
                                    'verify_peer_name' => false,
                                ],
                                'sslallow_self_signed' => true,
                                'sslverify_peer_name'  => false,
                            ])
                            ->setData(['file' => $data])
                            ->send();

                        if ($response->isOk) {
                            echo sprintf('File %s send to server%s', $file, PHP_EOL);
                        }
                    }
                }
            }
        }
    }
}

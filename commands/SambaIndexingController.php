<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SambaIndexingController extends Controller
{
    /**
     * @return int
     */
    public function actionIndex(): int {
        $this->scanFiles();

        return ExitCode::OK;
    }

    /**
     * @param string $remotePath
     *
     * @return void
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
                        echo $fullPath . PHP_EOL;
                    }
                }
            }
        }
    }
}
